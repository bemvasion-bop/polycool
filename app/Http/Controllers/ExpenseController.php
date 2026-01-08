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
        // =======================
        // 1️⃣ VALIDATION RULES
        // =======================
        $validated = $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'expense_type'  => 'required|in:material,custom',

            // MATERIAL
            'material_id'   => 'required_if:expense_type,material|nullable|exists:materials,id',
            'quantity_used' => 'required_if:expense_type,material|nullable|numeric|min:0.01',

            // CUSTOM
            'category'      => 'required_if:expense_type,custom|string|nullable',
            'amount'        => 'required_if:expense_type,custom|nullable|numeric|min:0.01',

            'expense_date'  => 'required|date',
            'description'   => 'nullable|string|max:255',
            'receipt'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // =======================
        // 2️⃣ BASE EXPENSE DATA
        // =======================
        $expense = new Expense();
        $expense->project_id   = $request->project_id;
        $expense->user_id      = auth()->id();
        $expense->expense_date = $request->expense_date;
        $expense->status       = 'pending';
        $expense->description  = $request->description;

        // =======================
        // 3️⃣ MATERIAL EXPENSE
        // =======================
        if ($request->expense_type === 'material') {

            $material = Material::findOrFail($request->material_id);

            $unitCost = $material->price_per_unit;
            $qty      = $request->quantity_used;
            $total    = $unitCost * $qty;

            $expense->expense_type  = 'material';
            $expense->material_id   = $material->id;
            $expense->supplier_id   = $material->supplier_id;
            $expense->unit_cost     = $unitCost;
            $expense->quantity_used = $qty;
            $expense->total_cost    = $total;
            $expense->amount        = $total;

            // =======================
            // 🔎 LEVEL 2 AUDIT
            // =======================
            $auditDetails =
                'Material Expense Added | ' .
                'Material: ' . $material->name .
                ' | Unit Cost: ₱' . number_format($unitCost, 2) .
                ' | Qty: ' . $qty .
                ' | Total: ₱' . number_format($total, 2) .
                ' | Project ID: ' . $expense->project_id;

        }

        // =======================
        // 4️⃣ CUSTOM EXPENSE
        // =======================
        else {

            $expense->expense_type  = 'custom';
            $expense->material_id   = null;
            $expense->supplier_id   = null;
            $expense->unit_cost     = null;
            $expense->quantity_used = null;
            $expense->total_cost    = $request->amount;
            $expense->amount        = $request->amount;
            $expense->category      = $request->category;

            // =======================
            // 🔎 LEVEL 2 AUDIT
            // =======================
            $auditDetails =
                'Custom Expense Added | ' .
                'Category: ' . $request->category .
                ' | Amount: ₱' . number_format($request->amount, 2) .
                ' | Project ID: ' . $expense->project_id;
        }

        // =======================
        // 5️⃣ RECEIPT UPLOAD
        // =======================
        if ($request->hasFile('receipt')) {
            $expense->receipt_path = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->save();



        return back()->with('success', 'Expense added successfully!');
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
        if (!in_array(auth()->user()->system_role, ['owner', 'accounting'])) {
            abort(403);
        }

        if ($expense->status !== Expense::STATUS_PENDING) {
            return back()->with('error', 'Only pending expenses can be approved.');
        }

        $expense->update([
            'status'           => Expense::STATUS_APPROVED,
            'processed_by'     => auth()->id(),
            'processed_reason' => 'Approved by ' . auth()->user()->given_name,
        ]);

        audit_log('Expense Approved', 'Expense ID '.$expense->id);

        return back()->with('success', 'Expense approved.');
    }


    public function saveReIssue(Request $request, Expense $expense)
    {
        if (!in_array(auth()->user()->system_role, ['manager', 'owner'])) {
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
        if (!in_array(auth()->user()->system_role, ['owner', 'accounting'])) {
            abort(403);
        }

        if ($expense->status !== Expense::STATUS_PENDING) {
            return back()->with('error', 'Only pending expenses can be rejected.');
        }

        $expense->update([
            'status'           => Expense::STATUS_REJECTED,
            'processed_by'     => auth()->id(),
            'processed_reason' => 'Rejected by ' . auth()->user()->given_name,
        ]);

        audit_log('Expense Rejected', 'Expense ID '.$expense->id);

        return back()->with('success', 'Expense rejected.');
    }


    public function requestReissue(Request $request, Expense $expense)
    {
        // Owner + Accounting only
        if (!in_array(auth()->user()->system_role, ['owner', 'accounting'])) {
            abort(403);
        }

        // Only pending expenses can be requested for re-issue
        if ($expense->status !== Expense::STATUS_PENDING) {
            return back()->with('error', 'Only pending expenses can be requested for re-issue.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $expense->update([
            'status'           => Expense::STATUS_REISSUE_REQUESTED,
            'processed_by'     => auth()->id(),
            'processed_reason' => 'Re-issue requested: ' . $request->reason,
        ]);

        // ❌ NO AUDIT LOG HERE (request only)

        return back()->with('success', 'Re-issue request sent successfully.');
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

        // Create corrected expense (new record)
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






}
