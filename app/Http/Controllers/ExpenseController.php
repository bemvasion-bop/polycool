<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Expense;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Helper: only owner + accounting can fully manage expenses.
     */
    protected function assertFinanceRole()
    {
        $user = Auth::user();

        if (!$user || !in_array($user->system_role, ['owner', 'accounting'])) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Display all expenses (OWNER + ACCOUNTING only)
     */
    public function index()
    {
        $this->assertFinanceRole();

        $expenses = Expense::with(['project', 'user'])
            ->orderBy('expense_date', 'desc')
            ->get();

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show a single expense
     * - Owner + accounting: can see everything
     * - Requestor: can view their own expense
     */
    public function show(Expense $expense)
    {
        $user = Auth::user();

        if (
            !$user ||
            !(
                in_array($user->system_role, ['owner', 'accounting']) ||
                $expense->user_id === $user->id
            )
        ) {
            abort(403, 'Unauthorized');
        }

        $expense->load(['project', 'user']);

        return view('expenses.show', compact('expense'));
    }

    /**
     * Show create expense form
     * (You can later limit this to manager+owner+accounting if you want)
     */
    public function create()
    {
        $projects = Project::orderBy('project_name')->get();
        return view('expenses.create', compact('projects'));
    }

    /**
     * Store new expense
     */
    public function store(Request $request)
    {
        // Detect if this is a material expense
        $isMaterial = $request->material_id ? true : false;

        // Validation
        $validated = $request->validate([
            'project_id'      => 'required|exists:projects,id',
            'material_id'     => 'nullable|exists:materials,id',
            'quantity_used'   => 'nullable|numeric|min:0',

            // Required only if NOT material expense
            'category'        => $isMaterial ? 'nullable' : 'required|string|max:255',
            'amount'          => $isMaterial ? 'nullable' : 'required|numeric|min:1',

            'expense_date'    => 'required|date',
            'receipt'         => 'nullable|image|max:2048',
            'description'     => 'nullable|string',
        ]);

        /**
         * MATERIAL EXPENSE HANDLING
         */
        if ($isMaterial) {
            $material = Material::find($request->material_id);

            $unitCost = $material->price_per_unit;
            $qty      = $request->quantity_used ?? 1;
            $total    = $unitCost * $qty;

            $validated['category']      = 'Material - ' . $material->name;
            $validated['amount']        = $total;
            $validated['supplier_id']   = $material->supplier_id ?? null;
            $validated['unit_cost']     = $unitCost;
            $validated['total_cost']    = $total;
        }

        /** Receipt upload */
        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['status']  = 'pending';

        $expense = Expense::create($validated);

        return redirect()
            ->route('projects.show', $expense->project_id)
            ->with('success', 'Expense added successfully and is now pending approval.');
    }

    /**
     * Show edit form
     * (Only owner + accounting, or the requestor while status is pending – optional)
     */
    public function edit(Expense $expense)
    {
        $user = Auth::user();

        $canEdit =
            in_array($user->system_role, ['owner', 'accounting']) ||
            ($expense->status === 'pending' && $expense->user_id === $user->id);

        if (!$canEdit) {
            abort(403, 'This expense can no longer be edited.');
        }

        $projects = Project::orderBy('project_name')->get();

        return view('expenses.edit', compact('expense', 'projects'));
    }

    /**
     * Update expense
     */
    public function update(Request $request, Expense $expense)
    {
        $user = Auth::user();

        $canEdit =
            in_array($user->system_role, ['owner', 'accounting']) ||
            ($expense->status === 'pending' && $expense->user_id === $user->id);

        if (!$canEdit) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'project_id'     => 'required|exists:projects,id',
            'category'       => 'required|string|max:255',
            'amount'         => 'required|numeric|min:1',
            'expense_date'   => 'required|date',
            'description'    => 'nullable|string',
            'receipt'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }

            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Delete expense
     */
    public function destroy(Expense $expense)
    {
        $this->assertFinanceRole();

        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return back()->with('success', 'Expense deleted successfully.');
    }

    /**
     * Approve expense (OWNER + ACCOUNTING)
     */
    public function approve(Expense $expense)
    {
        $this->assertFinanceRole();

        if ($expense->status !== 'pending') {
            return back()->with('info', 'This expense is already processed.');
        }

        $expense->update(['status' => 'approved']);

        return back()->with('success', 'Expense approved.');
    }

    /**
     * Reject expense (OWNER + ACCOUNTING)
     */
    public function reject(Expense $expense)
    {
        $this->assertFinanceRole();

        if ($expense->status !== 'pending') {
            return back()->with('info', 'This expense is already processed.');
        }

        $expense->update(['status' => 'rejected']);

        return back()->with('success', 'Expense rejected.');
    }
}
