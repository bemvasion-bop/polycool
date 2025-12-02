<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $materials = Material::with('supplier')->orderBy('name')->get();
        return view('materials.index', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $suppliers = Supplier::orderBy('name')->get();
        return view('materials.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category'       => 'nullable|string|max:255',
            'unit'           => 'nullable|string|max:50',
            'price_per_unit' => 'required|numeric|min:0',
            'supplier_id'    => 'nullable|exists:suppliers,id',
            'notes'          => 'nullable|string',
        ]);

        $material = Material::create($validated);

        return redirect()
            ->route('materials.show', $material->id)
            ->with('success', 'Material added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        //
        $material->load('supplier');
        return view('materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        //
        $suppliers = Supplier::orderBy('name')->get();
        return view('materials.edit', compact('material', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        //
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category'       => 'nullable|string|max:255',
            'unit'           => 'nullable|string|max:50',
            'price_per_unit' => 'required|numeric|min:0',
            'supplier_id'    => 'nullable|exists:suppliers,id',
            'notes'          => 'nullable|string',
        ]);

        $material->update($validated);

        return redirect()
            ->route('materials.show', $material->id)
            ->with('success', 'Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        //
        $material->delete();

        return redirect()
            ->route('materials.index')
            ->with('success', 'Material deleted successfully.');
    }
}
