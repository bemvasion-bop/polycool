<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\PayrollRun;
use App\Models\PayrollEntry;
use App\Models\CashAdvance;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /* ============================================================
     | 1. LIST PAYROLL RUNS
     ============================================================ */
    public function index()
    {
        $runs = PayrollRun::orderByDesc('created_at')->get();
        return view('payroll.index', compact('runs'));
    }

    /* ============================================================
     | 2. SHOW CREATE PAGE
     ============================================================ */
    public function create()
    {
        $employees = User::where('system_role', 'employee')
            ->orderBy('given_name')
            ->get();

        return view('payroll.create', compact('employees'));
    }

    /* ============================================================
     | 3. PREVIEW BEFORE GENERATION (PAYSLIP)
     ============================================================ */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $employee = User::findOrFail($data['employee_id']);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end   = Carbon::parse($data['end_date'])->endOfDay();

        /* ----------------------------------------------
         | OFFICE STAFF → Fixed Salary
         ---------------------------------------------- */
        if ($employee->employment_type === 'office_staff') {

            $monthly = $employee->monthly_salary ?? 0;
            $dailyRate = $monthly / 26;

            $daysPresent = Attendance::where('user_id', $employee->id)
                ->where('status', 'present')
                ->whereBetween('date', [$start, $end])
                ->count();

            $gross = $daysPresent * $dailyRate;
        }

        /* ----------------------------------------------
         | FIELD WORKER → Commission Based
         ---------------------------------------------- */
        else {
            $gross = $this->calculateCommission($employee, $start, $end);
            $daysPresent = null;
            $dailyRate = null;
        }

        /* ----------------------------------------------
         | CASH ADVANCES (Remaining amounts)
         ---------------------------------------------- */
        $cashAdvances = CashAdvance::where('user_id', $employee->id)
            ->where('status', 'approved')
            ->get();

        $remainingCA = $cashAdvances->sum(fn($ca) =>
            max(0, $ca->amount - $ca->deducted_amount)
        );

        $caDeduction = min($gross, $remainingCA);

        $net = $gross - $caDeduction;

        /* ----------------------------------------------
         | SLIP ARRAY
         ---------------------------------------------- */
        $slip = [
            'employee' => $employee,
            'days_present' => $daysPresent,
            'daily_rate' => $dailyRate,
            'gross_pay' => $gross,
            'cash_advance' => $caDeduction,
            'net_pay' => $net,
        ];

        return view('payroll.preview', compact('slip', 'start', 'end'));
    }

    /* ============================================================
     | 4. GENERATE PAYROLL (SAVE TO DATABASE)
     ============================================================ */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $employee = User::findOrFail($data['employee_id']);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end   = Carbon::parse($data['end_date'])->endOfDay();

        /* ----------------------------------------------
         | COMPUTE GROSS
         ---------------------------------------------- */
        if ($employee->employment_type === 'office_staff') {

            $monthly = $employee->monthly_salary ?? 0;
            $dailyRate = $monthly / 26;

            $daysPresent = Attendance::where('user_id', $employee->id)
                ->where('status', 'present')
                ->whereBetween('date', [$start, $end])
                ->count();

            $gross = $daysPresent * $dailyRate;

        } else {
            $gross = $this->calculateCommission($employee, $start, $end);
            $daysPresent = null;
            $dailyRate = null;
        }

        /* ----------------------------------------------
         | CASH ADVANCE DEDUCTION FIFO
         ---------------------------------------------- */
        $cashAdvances = CashAdvance::where('user_id', $employee->id)
            ->where('status', 'approved')
            ->get();

        $remainingCA = $cashAdvances->sum(fn($ca) =>
            max(0, $ca->amount - $ca->deducted_amount)
        );

        $caDeduction = min($gross, $remainingCA);

        $net = $gross - $caDeduction;

        /* ----------------------------------------------
         | SAVE TO DATABASE (ATOMIC)
         ---------------------------------------------- */
        DB::transaction(function () use (
            $start, $end, $employee, $gross, $caDeduction, $net, $cashAdvances
        ) {
            /* PAYROLL RUN */
            $run = PayrollRun::create([
                'payroll_type'     => $employee->employment_type === 'office_staff' ? 'office' : 'field',
                'period_start'     => $start,
                'period_end'       => $end,
                'status'           => 'draft',
                'total_gross'      => $gross,
                'total_deductions' => $caDeduction,
                'total_net'        => $net,
                'generated_by'     => auth()->id(),
            ]);

            /* ENTRY */
            PayrollEntry::create([
                'payroll_run_id' => $run->id,
                'user_id'        => $employee->id,
                'gross_pay'      => $gross,
                'deductions'     => $caDeduction,
                'net_pay'        => $net,
                'details'        => json_encode([
                    'coverage' => $start->format('M d, Y') . ' - ' . $end->format('M d, Y'),
                    'employment_type' => $employee->employment_type,
                ]),
            ]);

            /* DEDUCT CASH ADVANCES FIFO */
            $remaining = $caDeduction;

            foreach ($cashAdvances as $ca) {
                if ($remaining <= 0) break;

                $rem = $ca->amount - $ca->deducted_amount;
                if ($rem <= 0) continue;

                $apply = min($remaining, $rem);

                $ca->deducted_amount += $apply;

                if ($ca->deducted_amount >= $ca->amount) {
                    $ca->status = 'settled';
                }

                $ca->save();
                $remaining -= $apply;
            }
        });

        return redirect()->route('payroll.index')
            ->with('success', 'Payroll generated successfully.');
    }

    /* ============================================================
     | 5. SHOW PAYROLL RUN
     ============================================================ */
    public function show(PayrollRun $run)
    {
        $run->load('entries.user');
        return view('payroll.show', compact('run'));
    }

    /* ============================================================
     | 6. FINALIZE PAYROLL RUN
     ============================================================ */
    public function finalize(PayrollRun $run)
    {
        if ($run->status === 'finalized') {
            return back()->with('info', 'Already finalized.');
        }

        $run->update([
            'status' => 'finalized',
            'finalized_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payroll finalized successfully.');
    }

    /* ============================================================
     | COMMISSION CALCULATION (FIELD WORKERS)
     ============================================================ */
    private function calculateCommission(User $employee, $start, $end)
    {
        $projects = $employee->projects()
            ->whereNotNull('completed_date')
            ->whereBetween('completed_date', [$start, $end])
            ->get();

        $total = 0;

        foreach ($projects as $p) {

            $role = $p->pivot->role_in_project;

            $rate = match ($role) {
                'Spray Operator' => 0.40,
                'Technician'     => 0.30,
                'Helper 1'       => 0.15,
                'Helper 2'       => 0.15,
                default          => 0,
            };

            $total += ($p->contract_price ?? 0) * $rate;
        }

        return $total;
    }
}
