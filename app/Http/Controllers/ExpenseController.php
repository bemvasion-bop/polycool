<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\ExpenseHistory;
use App\Models\Expense;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

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
            'expense_type'  => 'required|in:material,custom',

            // Material mode
            'material_id'   => 'nullable|exists:materials,id',
            'quantity_used' => 'nullable|numeric|min:0.01',

            // Custom mode
            'category'      => 'nullable|string',
            'amount'        => 'nullable|numeric|min:0.01',

            'expense_date'  => 'required|date',
            'description'   => 'nullable|string|max:255',
            'receipt'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // Upload receipt if any
        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] =
                $request->file('receipt')->store('expense_receipts', 'public');
        }

        // 🚹 Who added this expense
        $validated['user_id'] = auth()->id();
        $validated['status']  = 'pending';

        /* ============================================================
        * MATERIAL EXPENSE MODE
        * ============================================================ */
        if ($request->expense_type === 'material') {

            $material = Material::findOrFail($request->material_id);

            $validated['material_id']   = $material->id;
            $validated['supplier_id']   = $material->supplier_id;
            $validated['unit_cost']     = $material->price_per_unit;
            $validated['quantity_used'] = $request->quantity_used;
            $validated['total_cost']    = $validated['unit_cost'] * $validated['quantity_used'];

            // Remove custom fields
            unset($validated['category'], $validated['amount']);
        }

        /* ============================================================
        * CUSTOM EXPENSE MODE
        * ============================================================ */
        if ($request->expense_type === 'custom') {

            $validated['category']   = $request->category;
            $validated['amount']     = $request->amount;
            $validated['total_cost'] = $request->amount; // Cost = amount

            // Remove material fields
            unset($validated['material_id'],
                $validated['supplier_id'],
                $validated['unit_cost'],
                $validated['quantity_used']);
        }

        Expense::create($validated);

        return back()->with('success', 'Expense submitted for approval.');
    }




    /* ============================================================
     *  CANCEL EXPENSE
     * ============================================================ */
    public function cancel(Expense $expense)
    {
        if (auth()->user()->system_role !== 'owner') {
            abort(403);
        }

        $expense->status = 'reversed';
        $expense->processed_by = auth()->id();
        $expense->processed_reason = 'Cancelled for re-issue by '.auth()->user()->given_name;
        $expense->save();

        // create draft replacement
        $newExpense = Expense::create([
            'project_id'        => $expense->project_id,
            'category'          => $expense->category, // CUSTOM only
            'expense_date'      => $expense->expense_date,
            'amount'            => $expense->amount,

            // MATERIAL details (safe even if null for custom)
            'material_id'       => $expense->material_id,
            'quantity_used'     => $expense->quantity_used,
            'unit_cost'         => $expense->unit_cost,
            'total_cost'        => $expense->total_cost,

            // Reset workflow
            'status'            => 'pending',
            'processed_by'      => null,
            'processed_reason'  => null,

            // Track history
            'reversal_of'       => $expense->id,

            // Owner/Manager will update this again
            'added_by'          => auth()->id(),
        ]);


        ExpenseHistory::create([
            'expense_id' => $expense->id,
            'changed_by' => auth()->id(),
            'old_amount' => $expense->amount ?? null,
            'change_reason' => 'Cancelled & re-issued by owner',
            'type' => 'cancel',
        ]);

        return back()->with('success', 'Expense cancelled and ready for manager correction.');
    }



        /* ============================================================
        *  REISSUE FORM
        * ============================================================ */
    public function reissueForm(Expense $expense)
    {
        return view('expenses.reissue', compact('expense'));
    }

    public function processReissue(Request $request, \App\Models\Expense $expense)
    {
        $request->validate([
            'correction_reason' => 'required|string|max:255'
        ]);

        $expense->status = 'reissued';
        $expense->correction_reason = $request->correction_reason;
        $expense->save();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense has been reissued successfully.');
    }


    public function reissue(Expense $expense)
    {
        // Only show modal for reversed expense's pending child
        $pending = Expense::where('reversal_of', $expense->id)
                        ->where('status', 'pending')
                        ->latest()
                        ->first();

        if (!$pending) {
            return back()->with('error', 'No pending re-issue record found.');
        }

        return view('expenses.reissue-modal', compact('pending'));
    }
    public function approve(Expense $expense)
    {
        if (!in_array(auth()->user()->system_role, ['owner','accounting'])) {
            abort(403);
        }

        $expense->status = 'approved';
        $expense->processed_by = auth()->id();
        $expense->processed_reason = 'Approved by '.auth()->user()->given_name;
        $expense->save();

        return back()->with('success', 'Expense approved successfully.');
    }

    public function saveReIssue(Request $request, Expense $expense)
    {
        if (auth()->user()->system_role !== 'manager') {
            abort(403);
        }

        if ($expense->status !== 'reissued') {
            return back()->with('info', 'Only reversed expenses can be re-issued.');
        }

        $oldAmount = $expense->amount;

        $expense->amount = $request->input('amount');
        $expense->description = $request->input('details') ?: 'Reissued corrected expense';
        $expense->status = 'pending';
        $expense->save();

        ExpenseHistory::create([
            'expense_id' => $expense->id,
            'changed_by' => auth()->id(),
            'old_amount' => $oldAmount,
            'new_amount' => $request->amount,
            'change_reason' => $request->details,
            'type' => 'amount',
        ]);


        return redirect()->route('projects.show', $expense->project_id)
            ->with('success', 'Corrected expense submitted for approval.');
    }



    public function reject(Expense $expense)
    {
        if (!in_array(auth()->user()->system_role, ['owner','accounting'])) {
            abort(403);
        }

        $expense->status = 'cancelled'; // or rejected if you want
        $expense->processed_by = auth()->id();
        $expense->processed_reason = 'Rejected by '.auth()->user()->given_name;
        $expense->save();

        return back()->with('success', 'Expense rejected.');
    }


    public function adjustQuantity(Request $request, Expense $expense)
    {
        if ($expense->material_id === null) abort(403);

        $request->validate([
            'new_quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string'
        ]);

        $oldQty = $expense->quantity_used;
        $oldTotal = $expense->total_cost;
        $unit = $expense->unit_cost;

        $newTotal = $unit * $request->new_quantity;

        // Reverse record
        Expense::create([
            'project_id' => $expense->project_id,
            'material_id' => $expense->material_id,
            'amount' => -$oldTotal,
            'status' => 'approved',
            'is_reversal' => 1,
            'description' => "Auto-reversal for quantity correction",
            'expense_date' => now()
        ]);

        // Update old to revised
        $expense->status = 'revised';
        $expense->save();

        // Create corrected expense
        Expense::create([
            'project_id' => $expense->project_id,
            'material_id' => $expense->material_id,
            'quantity_used' => $request->new_quantity,
            'unit_cost' => $unit,
            'amount' => $newTotal,
            'status' => 'pending',
            'description' => $request->reason,
            'expense_date' => now()
        ]);

        // Log
        ExpenseLog::create([
            'expense_id' => $expense->id,
            'user_id' => auth()->id(),
            'action' => 'quantity_adjusted',
            'old_amount' => $oldTotal,
            'new_amount' => $newTotal,
            'notes' => "Qty: $oldQty → ".$request->new_quantity
        ]);

        return back()->with('success', 'Quantity correction submitted for approval.');
    }


    public function adjustMaterialQuantity(Request $request, Expense $expense)
    {
        if (!$expense->material_id) {
            return back()->with('error', 'Only material expenses can adjust quantity.');
        }

        $request->validate([
            'new_quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
        ]);

        // Original values
        $oldQty = $expense->quantity_used;
        $oldTotal = $expense->total_cost;
        $unit = $expense->unit_cost;
        $newQty = $request->new_quantity;
        $newTotal = $unit * $newQty;

        // Reverse original
        Expense::create([
            'project_id'   => $expense->project_id,
            'material_id'  => $expense->material_id,
            'user_id'      => auth()->id(),
            'amount'       => -1 * $oldTotal,
            'unit_cost'    => $unit,
            'quantity_used'=> $oldQty,
            'total_cost'   => -1 * $oldTotal,
            'status'       => 'approved',
            'is_reversal'  => 1,
            'description'  => "Reversal of Qty correction for Expense #{$expense->id}",
            'expense_date' => now(),
        ]);

        // Mark original as revised
        $expense->update([
            'status'     => 'revised',
            'updated_at' => now(),
        ]);

        // Create corrected expense
        $corrected = Expense::create([
            'project_id'   => $expense->project_id,
            'material_id'  => $expense->material_id,
            'user_id'      => auth()->id(),
            'unit_cost'    => $unit,
            'quantity_used'=> $newQty,
            'amount'       => $newTotal,
            'total_cost'   => $newTotal,
            'status'       => 'pending',
            'description'  => "Correction: {$request->reason}",
            'expense_date' => now(),
        ]);

        return back()->with('success', 'Material quantity corrected and pending approval.');
    }


    // 🔁 Adjust Qty Handler
    public function adjustQty(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        // old values before correction
        $oldQty = $expense->quantity_used;
        $oldCost = $expense->amount;

        // update expense
        $expense->quantity_used = $request->quantity_used;
        $expense->amount = $expense->unit_cost * $request->quantity_used;

        // IMPORTANT: Send back to the approval flow
        $expense->status = 'pending';

        $expense->save();

        // A simple audit record for now (you can enhance later)
        ExpenseAudit::create([
            'expense_id' => $expense->id,
            'action' => 'adjust_quantity',
            'old_quantity' => $oldQty,
            'new_quantity' => $request->quantity_used,
            'old_amount' => $oldCost,
            'new_amount' => $expense->amount,
            'performed_by' => auth()->id()
        ]);

        return back()->with('success','Quantity correction submitted for approval!');
    }






}
