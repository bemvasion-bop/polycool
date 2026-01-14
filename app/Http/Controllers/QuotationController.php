<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Project;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;


class QuotationController extends Controller
{
    /**
     * List quotations.
     */
    public function index()
    {
        $quotations = Quotation::with('client', 'project')->get();


        return view('quotations.index', compact('quotations'));
    }
    /**
     * Show create form.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();

        return view('quotations.create', compact('clients'));
    }

    /**
     * Store quotation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'project_name'     => 'required|string|max:255',
            'address'          => 'nullable|string',

            // project info
            'scope_of_work'    => 'nullable|string',
            'system'           => 'nullable|string',
            'duration_days'    => 'nullable|numeric',

            // auto-calculated costing
            'total_bdft'       => 'required|numeric|min:0',
            'rate_per_bdft'    => 'required|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'contract_price'   => 'required|numeric|min:0',
            'down_payment'     => 'nullable|numeric|min:0',
            'balance'          => 'required|numeric|min:0',

            // items
            'items.*.substrate' => 'required|string',
            'items.*.thickness' => 'nullable|string',
            'items.*.volume'    => 'nullable|numeric|min:0',
        ]);

        // === SAFETY RECALCULATION (prevents tampering) ===
        $totalBdft = collect($request->items)->sum('volume');
        $contract  = ($totalBdft * $request->rate_per_bdft) - $request->discount;
        $balance   = $contract - $request->down_payment;

        // merge values
        $request->merge([
            'total_bdft'     => $totalBdft,
            'contract_price' => $contract,
            'balance'        => $balance,
        ]);

        // Create quotation
        $quotation = Quotation::create([
            'client_id'      => $request->client_id,
            'created_by'     => Auth::id(),
            'quotation_date' => now(),
            'project_name'   => $request->project_name,
            'address'        => $request->address,
            'system'         => $request->system,
            'scope_of_work'  => $request->scope_of_work,
            'duration_days'  => $request->duration_days,

            // Costing
            'total_bdft'      => $totalBdft,
            'rate_per_bdft'   => $request->rate_per_bdft,
            'discount'        => $request->discount,
            'contract_price'  => $contract,
            'down_payment'    => $request->down_payment ?? 0,
            'balance'         => $balance,

            'conditions'      => $request->conditions,
            'status'          => 'pending',
        ]);

        // Create items
        foreach ($request->items as $item) {
            if (!isset($item['substrate']) || $item['substrate'] === null) continue;

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'substrate'    => $item['substrate'],
                'thickness'    => $item['thickness'] ?? null,
                'volume'       => $item['volume'] ?? null,
            ]);
        }

        return redirect()->route('quotations.show', $quotation->id)
            ->with('success', 'Quotation created successfully.');
    }

    /**
     * Display single quotation.
     */
    public function show(Quotation $quotation)
    {
        $quotation->load('client', 'items');

        return view('quotations.show', compact('quotation'));
    }

    /**
     * Edit quotation (owner can edit even if converted).
     */
    public function edit(Quotation $quotation)
    {
        $user = Auth::user();

        if ($quotation->status === 'converted' && $user->role !== 'owner') {
            abort(403, 'Only the owner can edit a converted quotation.');
        }

        $clients = Client::orderBy('name')->get();
        $quotation->load('items');

        return view('quotations.edit', compact('quotation', 'clients'));
    }

    /**
     * Update quotation.
     */
    public function update(Request $request, Quotation $quotation)
    {
        $user = Auth::user();

        if ($quotation->status !== 'pending') {
            abort(403, 'Editing is only allowed while quotation is pending.');
        }



        // validation same as store
        $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'project_name'     => 'required|string|max:255',
            'address'          => 'nullable|string',

            'scope_of_work'    => 'nullable|string',
            'system'           => 'nullable|string',
            'duration_days'    => 'nullable|numeric',

            'total_bdft'       => 'required|numeric|min:0',
            'rate_per_bdft'    => 'required|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'contract_price'   => 'required|numeric|min:0',
            'down_payment'     => 'nullable|numeric|min:0',
            'balance'          => 'required|numeric|min:0',

            'items.*.substrate' => 'required|string',
            'items.*.thickness' => 'nullable|string',
            'items.*.volume'    => 'nullable|numeric|min:0',
        ]);

        // recalc
        $totalBdft = collect($request->items)->sum('volume');
        $contract  = ($totalBdft * $request->rate_per_bdft) - $request->discount;
        $balance   = $contract - $request->down_payment;

        $request->merge([
            'total_bdft'     => $totalBdft,
            'contract_price' => $contract,
            'balance'        => $balance,
        ]);

        // update quotation
        $quotation->update([
            'client_id'       => $request->client_id,
            'project_name'    => $request->project_name,
            'address'         => $request->address,
            'system'          => $request->system,
            'scope_of_work'   => $request->scope_of_work,
            'duration_days'   => $request->duration_days,

            'total_bdft'      => $totalBdft,
            'rate_per_bdft'   => $request->rate_per_bdft,
            'discount'        => $request->discount,
            'contract_price'  => $contract,
            'down_payment'    => $request->down_payment,
            'balance'         => $balance,

            'conditions'      => $request->conditions,
        ]);

        // Replace items
        $quotation->items()->delete();

        foreach ($request->items as $item) {
            if (!isset($item['substrate']) || $item['substrate'] === null) continue;

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'substrate'    => $item['substrate'],
                'thickness'    => $item['thickness'],
                'volume'       => $item['volume'],
            ]);
        }

        return redirect()->route('quotations.show', $quotation->id)
            ->with('success', 'Quotation updated successfully.');
    }

    /**
     * Approve quotation.
     */
    public function approve(Quotation $quotation)
    {
        if ($quotation->status !== 'pending') {
            return back()->with('error', 'Quotation cannot be approved.');
        }

        // Update quotation status
        $quotation->update(['status' => 'approved']);

        // Auto-create a Project
        Project::create([
            'quotation_id' => $quotation->id,
            'client_id'    => $quotation->client_id,
            'project_name' => $quotation->project_name,
            'location'     => $quotation->address ?? null,
            'total_price'  => $quotation->contract_price,
            'start_date'   => $quotation->quotation_date, // TEMP until you set schedule
            'status'       => 'pending', // IMPORTANT: Option A
        ]);

        return back()->with('success', 'Quotation approved! Project created.');
    }


    /**
     * Decline quotation.
     */

    public function decline(Quotation $quotation)
    {
        if ($quotation->status !== 'pending') {
            return back()->with('error', 'Quotation cannot be declined.');
        }

        $quotation->update(['status' => 'declined']);

        return back()->with('success', 'Quotation declined.');
    }

    /**
     * Convert to project.
     */
    public function convertToProject(Quotation $quotation)
    {
        DB::beginTransaction();

        try {
            // 1️⃣ GET OR CREATE PROJECT
            $project = $quotation->project ?? Project::create([
                'client_id'    => $quotation->client_id,
                'quotation_id' => $quotation->id,
                'project_name' => $quotation->project_name,
                'location'     => $quotation->address,
                'status'       => 'pending',
            ]);

            // 2️⃣ AUTO-IMPORT DOWNPAYMENT (ONCE ONLY)
            if (($quotation->down_payment ?? 0) > 0) {

                // guard: prevent duplicate import
                $exists = Payment::where('project_id', $project->id)
                    ->where('notes', 'Auto-imported from quotation downpayment')
                    ->exists();

                if (!$exists) {
                    $payment = Payment::create([
                        'project_id'     => $project->id,
                        'amount'         => $quotation->down_payment,
                        'payment_method' => 'cash',
                        'payment_date'   => $quotation->quotation_date ?? now(),
                        'notes'          => 'Auto-imported from quotation downpayment',
                        'status'         => 'approved',
                        'submitted_by'   => $quotation->created_by ?? auth()->id(),
                        'approved_by'    => auth()->id(),
                    ]);

                    $payment->update([
                        'reference_number' => 'DP-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
                    ]);
                }
            }

            // 3️⃣ MARK AS CONVERTED
            $quotation->update(['status' => 'converted']);

            DB::commit();

            return redirect()
                ->route('projects.show', $project->id)
                ->with('success', 'Project created and quotation converted!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }




    /**
     * Delete quotation.
     */
    public function destroy(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'Converted quotations cannot be deleted.');
        }

        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    /**
     * Export quotation to PDF.
     */
    public function downloadPdf(Quotation $quotation)
    {
        $quotation->load('client', 'items');

        $pdf = Pdf::loadView('quotations.pdf', compact('quotation'));

        return $pdf->download('Quotation-' . $quotation->id . '.pdf');
    }
}
