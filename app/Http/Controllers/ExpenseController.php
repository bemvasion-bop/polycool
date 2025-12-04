<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /* ============================================================
     *  LIST EXPENSES
     * ============================================================ */
    public function index()
    {
        $expenses = Expense::with(['project', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('expenses.index', compact('expenses'));
    }


    /* ============================================================
     *  STORE EXPENSE
     * ============================================================ */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'    => 'required|exists:projects,id',

            // MATERIAL MODE
            'material_id'   => 'nullable|exists:materials,id',
            'quantity_used' => 'nullable|numeric|min:0.01',

            // CUSTOM MODE
            'category'      => 'nullable|string',
            'amount'        => 'nullable|numeric|min:1',

            'expense_date'  => 'required|date',
            'description'   => 'nullable|string',

            'receipt'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // Handle file upload
        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] =
                $request->file('receipt')->store('expense_receipts', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['status']  = 'pending';

        /* ============================================================
        * MATERIAL EXPENSE MODE
        * ============================================================ */
        if ($request->material_id) {
            $material = \App\Models\Material::find($request->material_id);

            $validated['supplier_id'] = $material->supplier_id;
            $validated['material_id'] = $material->id;
            $validated['unit_cost']   = $material->price_per_unit;
            $validated['total_cost']  = $material->price_per_unit * $request->quantity_used;

            // Remove fields only used for custom category
            unset($validated['category']);
            unset($validated['amount']);
        }

        /* ============================================================
        * CUSTOM EXPENSE MODE
        * ============================================================ */
        if (!$request->material_id) {
            $validated['category'] = $request->category;
            $validated['amount']   = $request->amount;

            // Remove material fields
            unset($validated['material_id'],
                $validated['supplier_id'],
                $validated['unit_cost'],
                $validated['quantity_used'],
                $validated['total_cost']);
        }

        Expense::create($validated);

        return back()->with('success', 'Expense submitted for approval.');
    }



    /* ============================================================
     *  CANCEL EXPENSE
     * ============================================================ */
    public function cancel(Request $request, Expense $expense)
    {
        $request->validate([
            'correction_reason' => 'required|string',
        ]);

        $expense->update([
            'status'            => 'cancelled',
            'corrected_by'      => auth()->id(),
            'correction_reason' => $request->correction_reason,
        ]);

        return back()->with('success', 'Expense cancelled.');
    }


    /* ============================================================
     *  REISSUE FORM
     * ============================================================ */
    public function reissueForm(Expense $expense)
    {
        return view('expenses.reissue', compact('expense'));
    }


    /* ============================================================
     *  REISSUE EXPENSE
     * ============================================================ */
    public function reissue(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'amount'            => 'required|numeric|min:1',
            'category'          => 'required|string',
            'expense_date'      => 'required|date',
            'correction_reason' => 'required|string',
        ]);

        // cancel original
        $expense->update([
            'status'            => 'reissued',
            'corrected_by'      => auth()->id(),
            'correction_reason' => $validated['correction_reason'],
        ]);

        // create corrected record
        Expense::create([
            'project_id'       => $expense->project_id,
            'category'         => $validated['category'],
            'amount'           => $validated['amount'],
            'expense_date'     => $validated['expense_date'],
            'status'           => 'approved',
            'reversal_of'      => $expense->id,
            'corrected_by'     => auth()->id(),
            'correction_reason'=> $validated['correction_reason'],
            'user_id'          => auth()->id(),
        ]);

        return redirect()
            ->route('projects.show', $expense->project_id)
            ->with('success', 'Expense corrected and reissued.');
    }
}
