<?php

namespace App\Http\Controllers;

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
    public function approve($id)
    {
        $payment = Payment::findOrFail($id);

        $payment->update([
            'status'       => 'approved',
            'approved_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Payment approved.');
    }



    /* ============================================================
     * CANCEL PAYMENT (Reversal)
     * ============================================================ */
    public function cancel(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'correction_reason' => 'required|string',
        ]);

        // Reverse
        $payment->update([
            'status'            => 'cancelled',
            'correction_reason' => $request->correction_reason,
        ]);

        return back()->with('success', 'Payment cancelled.');
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
    public function reissue(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'correction_reason' => 'required|string',
        ]);

        $old = Payment::findOrFail($id);

        // Create corrected payment
        $new = Payment::create([
            'project_id'      => $old->project_id,
            'amount'          => $request->amount,
            'payment_method'  => $old->payment_method,
            'payment_date'    => now(),
            'notes'           => "Reissued payment",
            'status'          => 'approved',
            'submitted_by'    => auth()->id(),
            'approved_by'     => auth()->id(),
            'reversal_of'     => $old->id,
            'correction_reason' => $request->correction_reason,
        ]);

        // Mark old as cancelled
        $old->update([
            'status' => 'cancelled'
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Payment reissued successfully.');
    }
}
