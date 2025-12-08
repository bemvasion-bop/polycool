<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Payment;
use App\Models\Project;
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'      => 'required|exists:projects,id',
            'amount'          => 'required|numeric|min:1',
            'payment_method'  => 'required|string',
            'payment_date'    => 'required|date',
            'notes'           => 'nullable|string',
            'proof'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // FIXED: correct column name is proof_path
        if ($request->hasFile('proof')) {
            $validated['proof_path'] = $request->file('proof')->store('payment_proofs', 'public');
        }

        // Do NOT save wrong field
        unset($validated['proof']);

        // status + submitter
        $validated['status']       = 'pending';
        $validated['submitted_by'] = auth()->id();

        Payment::create($validated);

        return back()->with('success', 'Payment submitted for approval.');
    }




    /* ============================================================
     * VIEW PAYMENT DETAILS
     * ============================================================ */
    public function show($id)
    {
        $payment = Payment::with(['project', 'addedBy', 'approvedBy'])->findOrFail($id);
        return view('payments.show', compact('payment'));
    }



    /* ============================================================
     * APPROVE PAYMENT (Owner + Accounting)
     * ============================================================ */
    public function approve(Payment $payment)
    {
        $payment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payment approved successfully!');
    }


    public function reject(Payment $payment)
    {
        $payment->status = 'rejected';
        $payment->approved_by = null; // just to be safe
        $payment->cancel_reason = request('cancel_reason'); // optional
        $payment->save();

        return redirect()->back()->with('success', 'Payment rejected.');
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
            'project_id' => $payment->project_id,
            'amount' => 0,
            'status' => 'pending',
            'payment_method' => $payment->payment_method,
            'notes' => 'Re-issue payment',
            'payment_date' => now(),
            'added_by' => auth()->id()
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
        if (auth()->user()->system_role !== 'manager') {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:255'
        ]);

        // Preserve original amount only once
        if (!$payment->original_amount) {
            $payment->original_amount = $payment->amount;
        }

        $payment->amount = $request->amount;
        $payment->notes = $request->notes ?: 'Corrected re-issue payment';
        $payment->status = 'pending';
        $payment->payment_date = now();
        $payment->corrected_by = auth()->id();
        $payment->corrected_at = now();
        $payment->save();

        return redirect()
            ->route('projects.show', $payment->project_id)
            ->with('success', 'Payment re-issued successfully.');
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
