<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * List all payments.
     */
    public function index()
    {
        $payments = Payment::with(['project', 'addedBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payments.index', compact('payments'));
    }


    /**
     * Show full payment details (for Audit + Owner/Accounting).
     */
    public function show(Payment $payment)
    {
        $payment->load(['project', 'addedBy', 'approvedBy']);

        return view('payments.show', compact('payment'));
    }


    /**
     * Show create form.
     */
    public function create()
    {
        $projects = Project::orderBy('project_name')->get();

        return view('payments.create', compact('projects'));
    }


    /**
     * Store payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'     => 'required|exists:projects,id',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'payment_date'   => 'required|date',
            'notes'          => 'nullable|string|max:255',
        ]);

        $payment = Payment::create([
            'project_id'   => $request->project_id,
            'amount'       => $request->amount,
            'method'       => $request->method,
            'paid_at'      => $request->paid_at,
            'status'       => 'pending',
            'added_by'     => auth()->id(),
        ]);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment added. Awaiting approval.');
    }


    /**
     * Edit payment (Owner only).
     */
    public function edit(Payment $payment)
    {
        if (Auth::user()->system_role !== 'owner') {
            abort(403);
        }

        $projects = Project::orderBy('project_name')->get();

        return view('payments.edit', compact('payment', 'projects'));
    }


    /**
     * Update payment.
     */
    public function update(Request $request, Payment $payment)
    {
        if (Auth::user()->system_role !== 'owner') {
            abort(403);
        }

        $validated = $request->validate([
            'project_id'     => 'required|exists:projects,id',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'payment_date'   => 'required|date',
            'notes'          => 'nullable|string|max:255',
        ]);

        $payment->update($validated);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment updated successfully.');
    }


    /**
     * Approve payment (Accounting only).
     */
    public function approve(Payment $payment)
    {
        $payment->update([
            'status'       => 'approved',
            'approved_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Payment approved successfully.');
    }


    /**
     * Reject payment.
     */
    public function reject(Payment $payment)
    {
        $payment->update([
            'status'       => 'rejected',
            'approved_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Payment rejected.');
    }


    /**
     * Delete payment (Owner only).
     */
    public function destroy(Payment $payment)
    {
        if (Auth::user()->system_role !== 'owner') {
            abort(403);
        }

        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment deleted.');
    }


    public function pdf(Payment $payment)
    {
        $payment->load(['project','addedBy','approvedBy']);

        $pdf = \PDF::loadView('payments.pdf', compact('payment'))
                ->setPaper('A4','portrait');

        return $pdf->download('Payment-'.$payment->id.'.pdf');
    }

    public function downloadPdf(Payment $payment)
    {
        // For now just return the same view
        // you can add real PDF generation later
        return view('payments.show', compact('payment'));
    }

    public function cancel(Payment $payment)
    {
        if ($payment->status !== 'approved') {
            return back()->with('error', 'Only approved payments can be cancelled.');
        }

        $payment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'notes' => ($payment->notes ?? '') . ' [Cancelled]',
        ]);

        return back()->with('success', 'Payment has been cancelled.');
    }


    public function reissue(Request $request, Payment $payment)
    {
        if ($payment->status !== 'cancelled') {
            return back()->with('error', 'Payment must be cancelled before re-issuing.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Create corrected payment
        $new = $payment->project->payments()->create([
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'payment_date' => $validated['payment_date'],
            'notes' => '[Re-Issued] ' . ($validated['notes'] ?? ''),
            'status' => 'approved',  // or pending if you require approval
            'issued_from_payment_id' => $payment->id, // audit link
        ]);

        return redirect()
            ->route('projects.show', $payment->project_id)
            ->with('success', 'Corrected payment re-issued successfully.');
    }

    public function reissueForm(Payment $payment)
    {
        if ($payment->status !== 'cancelled') {
            return back()->with('error', 'Payment must be cancelled before re-issuing.');
        }

        $project = $payment->project;

        return view('payments.reissue', compact('payment', 'project'));
    }

}
