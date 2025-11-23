<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// For PDF (after you install barryvdh/laravel-dompdf)
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Owner sees all, others see their own
        $quotations = Quotation::with('client')
            ->when($user->role !== 'owner', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('quotations.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'valid_until'    => 'nullable|date',
            'tax_rate'       => 'nullable|numeric|min:0',
            'discount_amount'=> 'nullable|numeric|min:0',

            'items'                     => 'required|array|min:1',
            'items.*.description'       => 'required|string',
            'items.*.unit'              => 'nullable|string|max:50',
            'items.*.quantity'          => 'required|numeric|min:0',
            'items.*.unit_price'        => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        // compute subtotal
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $taxRate   = $data['tax_rate'] ?? 0;
        $taxAmount = ($taxRate / 100) * $subtotal;
        $discount  = $data['discount_amount'] ?? 0;
        $total     = $subtotal + $taxAmount - $discount;

        $quotation = Quotation::create([
            'client_id'        => $data['client_id'],
            'user_id'          => $user->id,
            'reference'        => 'Q-' . now()->format('Ymd-His') . '-' . $user->id,
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'status'           => 'pending',
            'valid_until'      => $data['valid_until'] ?? null,
            'subtotal'         => $subtotal,
            'tax_rate'         => $taxRate,
            'tax_amount'       => $taxAmount,
            'discount_amount'  => $discount,
            'total_amount'     => $total,
        ]);

        foreach ($data['items'] as $index => $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description'  => $item['description'],
                'unit'         => $item['unit'] ?? null,
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['unit_price'],
                'line_total'   => $item['quantity'] * $item['unit_price'],
                'sort_order'   => $index,
            ]);
        }

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load('client', 'items', 'user', 'approver', 'project');

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $user = Auth::user();

        if (! $quotation->canBeEditedBy($user)) {
            abort(403, 'You are not allowed to edit this quotation.');
        }

        $clients = Client::orderBy('name')->get();
        $quotation->load('items');

        return view('quotations.edit', compact('quotation', 'clients'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $user = Auth::user();

        if (! $quotation->canBeEditedBy($user)) {
            abort(403, 'You are not allowed to edit this quotation.');
        }

        $data = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'valid_until'    => 'nullable|date',
            'tax_rate'       => 'nullable|numeric|min:0',
            'discount_amount'=> 'nullable|numeric|min:0',

            'items'                     => 'required|array|min:1',
            'items.*.id'                => 'nullable|exists:quotation_items,id',
            'items.*.description'       => 'required|string',
            'items.*.unit'              => 'nullable|string|max:50',
            'items.*.quantity'          => 'required|numeric|min:0',
            'items.*.unit_price'        => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $taxRate   = $data['tax_rate'] ?? 0;
        $taxAmount = ($taxRate / 100) * $subtotal;
        $discount  = $data['discount_amount'] ?? 0;
        $total     = $subtotal + $taxAmount - $discount;

        $quotation->update([
            'client_id'        => $data['client_id'],
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'valid_until'      => $data['valid_until'] ?? null,
            'subtotal'         => $subtotal,
            'tax_rate'         => $taxRate,
            'tax_amount'       => $taxAmount,
            'discount_amount'  => $discount,
            'total_amount'     => $total,
        ]);

        // Sync items (simple approach: delete and re-create)
        $quotation->items()->delete();

        foreach ($data['items'] as $index => $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description'  => $item['description'],
                'unit'         => $item['unit'] ?? null,
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['unit_price'],
                'line_total'   => $item['quantity'] * $item['unit_price'],
                'sort_order'   => $index,
            ]);
        }

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $user = Auth::user();

        // Optional: only owner can delete, and only if not converted
        if ($quotation->isConverted() || $user->role !== 'owner') {
            abort(403, 'Cannot delete this quotation.');
        }

        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    public function approve(Quotation $quotation)
    {
        $user = Auth::user();

        if ($user->role !== 'owner') {
            abort(403, 'Only owner can approve quotations.');
        }

        $quotation->update([
            'status'      => 'approved',
            'approved_by' => $user->id,
        ]);

        return back()->with('success', 'Quotation approved.');
    }

    public function decline(Quotation $quotation)
    {
        $user = Auth::user();

        if ($user->role !== 'owner') {
            abort(403, 'Only owner can decline quotations.');
        }

        $quotation->update([
            'status'      => 'declined',
            'approved_by' => $user->id,
        ]);

        return back()->with('success', 'Quotation declined.');
    }

    public function convertToProject(Quotation $quotation)
    {
        $user = Auth::user();

        if (! $quotation->isApproved() && $user->role !== 'owner') {
            abort(403, 'Only approved quotations can be converted.');
        }

        if ($quotation->isConverted()) {
            return back()->with('info', 'This quotation is already converted to a project.');
        }

        // Adjust fields to match your actual projects table
        $project = Project::create([
            'client_id'  => $quotation->client_id,
            'name'       => $quotation->title,
            'description'=> $quotation->description,
            'status'     => 'pending', // or whatever you use
            // add other fields if required (start_date, end_date, etc.)
        ]);

        $quotation->update([
            'status'               => 'converted',
            'converted_project_id' => $project->id,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Quotation converted to project successfully.');
    }

    public function downloadPdf(Quotation $quotation)
    {
        $quotation->load('client', 'items', 'user', 'approver');

        $pdf = Pdf::loadView('quotations.pdf', [
            'quotation' => $quotation,
        ]);

        $filename = $quotation->reference . '.pdf';

        return $pdf->download($filename);
    }
}
