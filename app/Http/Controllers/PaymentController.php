<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Payment;
use App\Models\Project;
use App\Models\PayrollRun;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /* ============================================================
     * LIST PAYMENTS (Owner + Accounting)
     * ============================================================ */
    public function index()
    {
        $payments = Payment::with(['project', 'addedBy', 'approvedBy'])
            ->latest()
            ->get();

        return view('payments.index', compact('payments'));
    }



    /* ============================================================
     * STORE NEW PAYMENT (Manager, Owner, Accounting)
     * ============================================================ */
    public function store(Request $request, Payment $payment)
    {
        if (!in_array(auth()->user()->system_role, ['manager','owner'])) {
            abort(403);
        }

        $validated = $request->validate([
            'project_id'      => 'required|exists:projects,id',
            'amount'          => 'required|numeric|min:1',
            'payment_method'  => 'required|string',
            'payment_date'    => 'required|date',
            'notes'           => 'nullable|string',
            'proof'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        if ($request->hasFile('proof')) {
            $validated['proof_path'] = $request->file('proof')
                ->store('payment_proofs', 'public');
        }

        unset($validated['proof']);

        $validated['status']       = 'pending';
        $validated['submitted_by'] = auth()->id();

        $payment = Payment::create($validated);

        audit_log(
            'Payment Added',
            'Added payment ₱' . number_format($payment->amount, 2) .
            ' to Project ID: ' . $payment->project_id
        );

        audit_log(
            'Payment Added',
            "Payment ID {$payment->id} added by ".auth()->user()->given_name
        );



        return back()->with('success', 'Payment submitted for approval.');




    }



    /* ============================================================
     * VIEW PAYMENT DETAILS
     * ============================================================ */
    public function show($id)
    {
        $payment = Payment::with(['project','addedBy','approvedBy'])
            ->findOrFail($id);

        return view('payments.show', compact('payment'));
    }

     public function requestReissue(Payment $payment)
    {
        if (!in_array(auth()->user()->system_role, ['owner', 'accounting'])) {
            abort(403);
        }

        $payment->update([
            'status' => 'reissue_requested',
        ]);

        audit_log(
            'Payment Re-issue Requested',
            'Re-issue requested for payment ID: '.$payment->id
        );

        return back()->with('success', 'Re-issue requested.');
    }


    /* ============================================================
     * APPROVE PAYMENT (Owner + Accounting)
     * ============================================================ */
    public function approve(Payment $payment)
    {
        if (!in_array(auth()->user()->system_role, ['owner', 'accounting'])) {
            abort(403);
        }

        $payment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        audit_log(
            'Payment Approved',
            'Approved payment ID: '.$payment->id
        );

        return back()->with('success', 'Payment approved successfully.');
    }



    public function reject(Payment $payment)
    {
        if (!in_array(auth()->user()->system_role, ['owner', 'accounting'])) {
            abort(403);
        }

        $payment->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        audit_log(
            'Payment Rejected',
            'Rejected payment ID: '.$payment->id
        );

        return back()->with('success', 'Payment rejected.');
    }


    public function finalize(PayrollRun $run)
    {
        $run->update(['status' => 'finalized']);

        audit_log(
            'Payroll Finalized',
            'Payroll run ID: ' . $run->id
        );

        return back()->with('success', 'Payroll finalized.');
    }

    /* ============================================================
     * CANCEL PAYMENT (Reversal)
     * ============================================================ */
    public function cancel(Payment $payment)
    {
        // Allowed roles only
        if (!in_array(auth()->user()->system_role, ['owner','accounting'])) {
            abort(403, 'Unauthorized action');
        }

        // Only cancel approved payments
        if ($payment->status !== 'approved') {
            return back()->with('error', 'Only approved payments can be cancelled.');
        }

        // Mark as reversed
        $payment->status = 'reversed';
        $payment->notes = 'Cancelled & marked for re-issue';
        $payment->approved_by = auth()->id();
        $payment->save();

        // Create a replacement payment (empty)
        $newPayment = Payment::create([
            'project_id'       => $payment->project_id,
            'amount'           => 0,
            'status'           => 'pending',
            'payment_method'   => $payment->payment_method,
            'notes'            => 'Re-issue payment',
            'payment_date'     => now(),
            'submitted_by'     => auth()->id(),
            'added_by'         => auth()->id(),
            'reference_number' => 'REISSUE-TEMP-' . $payment->id,
        ]);

        return redirect()
            ->route('payments.show', $payment->id)
            ->with('success', 'Payment cancelled. Please re-issue corrected amount.');
    }




    /* ============================================================
     * REISSUE PAYMENT FORM
     * ============================================================ */
    public function reissueForm($id)
    {
        $payment = Payment::findOrFail($id);

        return view('payments.reissue', compact('payment'));
    }



    /* ============================================================
     * REISSUE PAYMENT (Corrected Amount)
     * ============================================================ */
    public function reissue(Request $request, Payment $payment)
    {
        // Manager only
        if (auth()->user()->system_role !== 'manager') {
            abort(403, 'Unauthorized action');
        }

        // Must be reversed first
        if ($payment->status !== 'reversed') {
            return back()->with('error', 'Only reversed payments can be re-issued.');
        }

        $validated = $request->validate([
            'amount'            => 'required|numeric|min:1',
            'payment_date'      => 'required|date',
            'correction_reason' => 'required|string|max:255',
        ]);

        // Preserve original amount ONCE
        if (is_null($payment->original_amount)) {
            $payment->original_amount = $payment->amount;
        }

        $reference = 'REISSUE-' . $payment->id . '-' . now()->timestamp;

        $payment->update([
            'amount'           => $validated['amount'],
            'payment_date'     => $validated['payment_date'],
            'reference_number' => $reference,
            'notes'            => 'Re-issued: ' . $validated['correction_reason'],
            'status'           => 'pending',
            'submitted_by'     => auth()->id(),
            'approved_by'      => null,
            'corrected_at'     => now(),
        ]);

        return redirect()
            ->route('projects.show', $payment->project_id)
            ->with('success', 'Payment re-issued and submitted for approval.');
    }



    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $payment->amount = $request->amount;
        $payment->payment_date = $request->payment_date;
        $payment->notes = $request->notes ?? null;

        $payment->save();

        return redirect()->back()->with('success', 'Payment updated successfully!');
    }


    public function history(Payment $payment)
    {
        $original = Payment::find($payment->reversal_of);

        return response()->json([
            'original_amount' => $original ? $original->amount : '—',
            'cancelled_by' => $original ? optional($original->canceller)->name : '—',
            'cancel_reason' => $original ? ($original->cancel_reason ?? '—') : '—',
            'new_amount' => $payment->amount, // corrected payment
            'correction_reason' => $payment->notes,
            'updated_at' => $payment->updated_at->format('M d, Y h:i A'),
        ]);
    }


    public function printSummary(Project $project)
    {
        $payments = Payment::where('project_id', $project->id)->get();

        $baseContract = $project->total_price;
        $extraTotal   = $project->extraWorks()->sum('amount');
        $totalProject = $baseContract + $extraTotal;

        $totalPaid    = $payments->sum('amount');
        $remaining    = $totalProject - $totalPaid;

        $pdf = Pdf::loadView('pdf.payment-summary', [
            'project'      => $project,
            'payments'     => $payments,
            'baseContract' => $baseContract,
            'extraTotal'   => $extraTotal,
            'totalProject' => $totalProject,
            'totalPaid'    => $totalPaid,
            'remaining'    => $remaining,
        ])->setPaper('A4', 'portrait');

        return $pdf->download("Payment-Summary-{$project->project_name}.pdf");
    }

    public function printAudit(Payment $payment)
    {
        $original = Payment::find($payment->reversal_of);
        $replaced = $payment; // This is already the new corrected record

        $pdf = Pdf::loadView('pdf.reversal-audit', [
            'payment'   => $payment,
            'original'  => $original,
            'replaced'  => $replaced,
        ])->setPaper('A4', 'portrait');

        return $pdf->download("Reversal-Audit-{$payment->id}.pdf");
    }

}
