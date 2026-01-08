<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\Material;
use App\Models\ProjectExtraWork;
use App\Models\ProjectProgressLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\WeatherService;

class ProjectController extends Controller
{
    /**
     * Display all projects.
     */
    public function index()
    {
        $projects = Project::with(['client', 'expenses', 'payments'])->get();

        foreach ($projects as $p) {
            $p->weatherData = null; // skip weather to save time
            $p->progress = $p->calculateProgress();
        }

        return view('projects.index', compact('projects'));
    }

    /**
     * Show specific project.
     */
    public function show(Project $project, WeatherService $weatherService)
    {
        $project->load([
            'client',
            'quotation',
            'expenses.user',
            'expenses.material.supplier',
            'payments.addedBy.user',
            'payments.correctedBy.user',
            'users',
            'extraWorks.addedBy',
        ]);

        $materials = Material::with('supplier')->orderBy('name')->get();

        // Weather
        $weatherData = null;
        if (!empty($project->location)) {
            $weatherData = $weatherService->getForecast($project->location);
        }

        // Progress (view-only)
        $project->progress = $project->calculateProgress();

        // ===============================
        // AUTO-IMPORT QUOTATION DOWNPAYMENT (IDEMPOTENT)
        // ===============================
        $quotation = $project->quotation;
        $downpayment = (float) ($quotation->downpayment_amount ?? 0);

        if ($quotation && $downpayment > 0) {
            $project->payments()->updateOrCreate(
                [
                    'project_id' => $project->id,
                    'notes'      => 'Auto-imported from quotation downpayment',
                ],
                [
                    'amount'         => $downpayment,
                    'payment_method' => $quotation->downpayment_method ?? 'cash',
                    'status'         => 'approved',
                    'payment_date'   => $quotation->downpayment_date ?? $quotation->created_at,
                    'added_by'       => null,
                    'corrected_by'   => null,
                ]
            );
        }

        // ===============================
        // FINANCIALS (SINGLE SOURCE OF TRUTH)
        // ===============================
        $baseContract = $quotation->contract_price ?? 0;
        $extraTotal   = $project->approvedExtraWorks()->sum('amount');
        $totalProject = $baseContract + $extraTotal;

        $totalPaid = $project->approvedPayments()->sum('amount');
        $remainingBalance = $totalProject - $totalPaid;

        // ✅ Paid indicator only (NO DB UPDATE)
        $isFullyPaid = $remainingBalance <= 0;

        // ===============================
        // EXPENSES (APPROVED ONLY)
        // ===============================
        $totalApprovedMaterial = $project->expenses()
            ->where('status', 'approved')
            ->whereNotNull('material_id')
            ->sum('total_cost');

        $totalApprovedCustom = $project->expenses()
            ->where('status', 'approved')
            ->whereNull('material_id')
            ->sum('amount');

        $totalApprovedExpenses = $totalApprovedMaterial + $totalApprovedCustom;
        $remainingAfterExpenses = $totalProject - $totalApprovedExpenses;

        // Lists
        $extraWorks = $project->extraWorks()->with('addedBy')->get();
        $payments = $project->payments()
            ->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('projects.show', compact(
            'project',
            'materials',
            'extraWorks',
            'payments',
            'weatherData',
            'baseContract',
            'extraTotal',
            'totalProject',
            'totalPaid',
            'remainingBalance',
            'isFullyPaid',
            'totalApprovedExpenses',
            'remainingAfterExpenses'
        ));
    }




    /**
     * Edit project.
     */
    public function edit(Project $project)
    {
        // Load employees that can be assigned
        $employees = User::where('system_role', 'employee')
            ->where('employment_type', 'field_worker')
            ->orderBy('given_name')
            ->get();

        // Roles dropdown (from constant)
        $projectRoles = array_keys(Project::ROLE_SHARES);

        return view('projects.edit', compact('project', 'employees', 'projectRoles'));
    }

    /**
     * Update project fully.
     */
    public function update(Request $request, Project $project)
    {
        // ----------------------------
        // 1. VALIDATE BASIC FIELDS
        // ----------------------------
        $validated = $request->validate([
            'location'   => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'status'     => 'nullable',
        ]);

        // Normalize dates
        if ($request->start_date) {
            $validated['start_date'] = date('Y-m-d', strtotime($request->start_date));
        }

        if ($request->end_date) {
            $validated['end_date'] = date('Y-m-d', strtotime($request->end_date));
        }

        // Convert to Carbon
        $startDate = !empty($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;
        $endDate   = !empty($validated['end_date'])   ? Carbon::parse($validated['end_date'])   : null;

        // ----------------------------------------
        // 2. AUTO STATUS LOGIC
        // ----------------------------------------
        if (!$startDate && !$endDate) {
            $validated['status'] = 'pending';
        } elseif ($startDate && $startDate->isFuture()) {
            $validated['status'] = 'pending';
        } elseif ($endDate && $endDate->isPast()) {
            $validated['status'] = 'completed';
        } elseif ($startDate && $startDate->isPast() && !$endDate) {
            $validated['status'] = 'active';
        } elseif ($startDate && $startDate->isPast() && $endDate && $endDate->isFuture()) {
            $validated['status'] = 'active';
        } else {
            $validated['status'] = 'pending';
        }

        // Save basic project changes
        $project->update($validated);

        // ==================================================
        // 3. HANDLE WORKFORCE ASSIGNMENT
        // ==================================================
        $employees = $request->employees ?? [];
        $roles     = $request->roles ?? [];

        $syncData = [];

        foreach ($employees as $empId) {
            $emp = User::find($empId);

            // ❌ Office staff
            if ($emp && $emp->employment_type === 'office_staff') {
                $error = $emp->full_name . ' is office staff and cannot be assigned.';
                return back()->with('error', $error);
            }

            // ❌ Already active in another project
            if (Project::employeeHasActiveProject($empId) &&
                !$project->users()->where('user_id', $empId)->exists()
            ) {
                $error = $emp->full_name . ' is still assigned to another ongoing project.';
                return back()->with('error', $error);
            }

            $syncData[$empId] = [
                'role_in_project' => $roles[$empId] ?? null,
                'assigned_at'     => now(),
            ];
        }


        $project->users()->sync($syncData);

        // ==================================================
        // 4. AUDIT LOG
        // ==================================================
        audit_log(
            'Project Updated',
            'Updated project: ' . $project->project_name .
            ' (ID: ' . $project->id . ')'
        );
        // =======================================
        // AUDIT LOG (LEVEL 2)
        // =======================================
        if (!empty($validated)) {
            audit_log(
                'Project Updated',
                'Updated fields: ' . implode(', ', array_keys($validated)) .
                ' | Project: ' . $project->project_name .
                ' (ID: ' . $project->id . ')'
            );
        }


        // ----------------------------------------
        // 5. DONE
        // ----------------------------------------
        if (!empty($employees)) {
             return redirect()
                ->route('projects.show', $project->id)
                ->with('success', 'Assigned workforce updated successfully!');
        } else {
            return redirect()
                ->route('projects.show', $project->id)
                ->with('success', 'Project updated successfully.');
        }
    }

    /**
     * Delete project
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /* ==========================================================
     |  EXTRA WORK ACTIONS
     ========================================================== */

    /**
     * Store a new extra work line for a project.
     */
    public function storeExtraWork(Request $request, Project $project)
    {
        $data = $request->validate([
            'description'   => 'required|string|max:255',
            'volume_bdft'   => 'nullable|numeric|min:0',
            'rate_per_bdft' => 'nullable|numeric|min:0',
            'amount'        => 'nullable|numeric|min:0',
        ]);

        // If amount not manually provided, compute from bd.ft * rate
        if (($data['amount'] ?? null) === null) {
            $vol  = $data['volume_bdft']   ?? 0;
            $rate = $data['rate_per_bdft'] ?? 0;
            $data['amount'] = $vol * $rate;
        }

        $data['project_id'] = $project->id;
        $data['added_by']   = auth()->id();

        ProjectExtraWork::create($data);

        return back()->with('success', 'Extra work added to project.');
    }




    public function storeProgress(Request $request, Project $project)
    {
        $data = $request->validate([
            'log_date'       => 'required|date',
            'bdft_completed' => 'required|numeric|min:0',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Prevent over logging total bd.ft
        $totalBdft = $project->quotation->total_bdft ?? 0;
        $loggedBdft = $project->progressLogs()->sum('bdft_completed');
        $newTotal = $loggedBdft + $data['bdft_completed'];

        if ($newTotal > $totalBdft) {
            return back()->withErrors(['bdft_completed' => 'Cannot exceed the total project bd.ft']);
        }

        $data['project_id'] = $project->id;
        $data['user_id'] = auth()->id();

        ProjectProgressLog::create($data);

        return back()->with('success', 'Progress logged successfully.');
    }

    public function approveExtraWork(Project $project, ProjectExtraWork $extraWork)
    {
        if ($extraWork->project_id !== $project->id) {
            abort(404);
        }

        $extraWork->status = 'approved';
        $extraWork->save();

        // AUTO-UPDATE PROJECT FINANCIALS (Recalculate using current approved extra works)
        $approvedTotal = $project->extraWorks()->where('status', 'approved')->sum('amount');
        $project->total_extra_cost = $approvedTotal;
        $project->save();

        return back()->with('success', 'Extra work approved and financials updated.');
    }


    public function rejectExtraWork(Project $project, ProjectExtraWork $extraWork)
    {
        if ($extraWork->project_id !== $project->id) {
            abort(404);
        }

        $extraWork->status = 'rejected';
        $extraWork->save();

        return back()->with('success', 'Extra work was rejected.');
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->approvedPayments()->sum('amount') >=
            (($this->quotation->contract_price ?? 0) +
                $this->approvedExtraWorks()->sum('amount'));
    }
}
