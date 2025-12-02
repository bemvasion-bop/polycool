<?php

namespace App\Http\Controllers;

use App\Models\CashAdvance;
use App\Models\User;
use Illuminate\Http\Request;

class CashAdvanceController extends Controller
{
    /* List all cash advance requests */
    public function index()
    {
        $advances = CashAdvance::with('employee')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashadvance.index', compact('advances'));
    }

    /* Show form */
    public function create()
    {
        $employees = User::where('system_role', 'employee')
            ->orderBy('last_name')
            ->orderBy('given_name')
            ->get();    

        return view('cashadvance.create', compact('employees'));
    }

    /* Save record */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'request_date' => 'required|date',
        ]);

        CashAdvance::create($request->all());

        return redirect()
            ->route('cashadvance.index')
            ->with('success', 'Cash advance request recorded.');
    }

    /* Edit form */
    public function edit(CashAdvance $cashadvance)
    {
        $employees = User::where('system_role', 'employee')->get();

        return view('cashadvance.edit', compact('cashadvance', 'employees'));
    }

    /* Update record */
    public function update(Request $request, CashAdvance $cashadvance)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'request_date' => 'required|date',
        ]);

        $cashadvance->update($request->all());

        return redirect()
            ->route('cashadvance.index')
            ->with('success', 'Cash advance updated successfully.');
    }

    /* Delete */
    public function destroy(CashAdvance $cashadvance)
    {
        $cashadvance->delete();
        return redirect()->route('cashadvance.index');
    }


    /* APPROVE */
    public function approve(CashAdvance $advance)
    {
        $advance->status = 'approved';
        $advance->approved_by = auth()->id();
        $advance->save();

        return back()->with('success', 'Cash advance request approved.');
    }

    /* REJECT */
    public function reject(CashAdvance $advance)
    {
        $advance->status = 'rejected';
        $advance->approved_by = auth()->id();
        $advance->save();

        return back()->with('success', 'Cash advance request rejected.');
    }

    // Show own cash advance history
    public function myRequests()
    {
        $requests = CashAdvance::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashadvance.employee.index', compact('requests'));
    }

    // Show employee request form
    public function myCreate()
    {
        return view('cashadvance.employee.create');
    }

    // Save employee request
    public function myStore(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'request_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        CashAdvance::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'request_date' => $request->request_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('cashadvance.my')
            ->with('success', 'Your cash advance request has been submitted.');
    }


    /* ============================================================
    | EMPLOYEE: Request Cash Advance (Form)
    ============================================================ */
    public function requestForm()
    {
        return view('cashadvance.employee.request');
    }


    /* ============================================================
    | EMPLOYEE: Submit Cash Advance
    ============================================================ */
    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100|max:50000',
            'reason' => 'required|string|max:255',
        ]);

        CashAdvance::create([
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'request_date' => now()->toDateString(),
        ]);

        return redirect()->route('cashadvance.myRequests')
            ->with('success', 'Your cash advance request has been submitted.');
    }


    
}
