<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\Material;
use App\Models\ProjectExtraWork;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\WeatherService;

class ProjectController extends Controller
{
    /**
     * Display all projects.
     */
    public function index()
    {
        $projects = Project::with(['client', 'expenses', 'payments'])->get();

        foreach ($projects as $p) {
            $p->weatherData = null; // skip weather to save time
            $p->progress = $p->calculateProgress();
        }

        return view('projects.index', compact('projects'));
    }

    /**
     * Show specific project.
     */
    public function show(Project $project, WeatherService $weatherService)
    {
        $project->load([
            'client',
            'expenses.user',
            'payments.submitter',
            'users',
            'extraWorks.addedBy',
        ]);


        $materials = Material::with('supplier')->orderBy('name')->get();


        // Weather
        $weatherData = $weatherService->getForecast($project->location);
        $project->progress = $project->calculateProgress();

        // Financials
        $basePrice         = $project->base_contract_price;
        $extraWorkTotal    = $project->extra_work_total;
        $totalProjectPrice = $project->final_project_price;

        $totalPaid = $project->payments()
            ->where('status', 'approved')
            ->sum('amount');

        // Expense Financials — Only APPROVED counted
        $totalApprovedMaterial = $project->expenses()
            ->where('status', 'approved')
            ->whereNotNull('material_id')
            ->sum('total_cost');

        $totalApprovedCustom = $project->expenses()
            ->where('status', 'approved')
            ->whereNull('material_id')
            ->sum('amount');

        $totalApprovedExpenses = $totalApprovedMaterial + $totalApprovedCustom;

        $remainingAfterExpenses = ($totalProjectPrice ?? 0) - $totalApprovedExpenses;

        $remainingBalance = ($totalProjectPrice ?? 0) - $totalPaid;

        $extraWorks = $project->extraWorks()->with('addedBy')->get();
        $payments = $project->payments()->orderBy('created_at', 'desc')->get();


        return view('projects.show', compact(
  'project',
 'payments',
            'materials',
            'extraWorks',
            'basePrice',
            'extraWorkTotal',
            'totalProjectPrice',
            'totalPaid',
            'remainingBalance',
            'totalApprovedExpenses',
            'remainingAfterExpenses',
            'weatherData',
        ));
    }




    /**
     * Edit project.
     */
    public function edit(Project $project)
    {
        // Load employees that can be assigned
        $employees = User::where('system_role', 'employee')
            ->where('employment_type', 'field_worker')
            ->orderBy('given_name')
            ->get();

        // Roles dropdown (from constant)
        $projectRoles = array_keys(Project::ROLE_SHARES);

        return view('projects.edit', compact('project', 'employees', 'projectRoles'));
    }

    /**
     * Update project fully.
     */
    public function update(Request $request, Project $project)
    {
        // ----------------------------
        // 1. VALIDATE BASIC FIELDS
        // ----------------------------
        $validated = $request->validate([
            'location'   => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'status'     => 'nullable',
        ]);

        // Normalize dates
        if ($request->start_date) {
            $validated['start_date'] = date('Y-m-d', strtotime($request->start_date));
        }

        if ($request->end_date) {
            $validated['end_date'] = date('Y-m-d', strtotime($request->end_date));
        }

        // Convert to Carbon
        $startDate = !empty($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;
        $endDate   = !empty($validated['end_date'])   ? Carbon::parse($validated['end_date'])   : null;

        // ----------------------------------------
        // 2. AUTO STATUS LOGIC
        // ----------------------------------------
        if (!$startDate && !$endDate) {
            $validated['status'] = 'pending';
        } elseif ($startDate && $startDate->isFuture()) {
            $validated['status'] = 'pending';
        } elseif ($endDate && $endDate->isPast()) {
            $validated['status'] = 'completed';
        } elseif ($startDate && $startDate->isPast() && !$endDate) {
            $validated['status'] = 'active';
        } elseif ($startDate && $startDate->isPast() && $endDate && $endDate->isFuture()) {
            $validated['status'] = 'active';
        } else {
            $validated['status'] = 'pending';
        }

        // Save basic project changes
        $project->update($validated);

        // ==================================================
        // 3. HANDLE WORKFORCE ASSIGNMENT
        // ==================================================
        $employees = $request->employees ?? [];
        $roles     = $request->roles ?? [];

        $syncData = [];

        foreach ($employees as $empId) {
            $emp = User::find($empId);

            // ❌ Office staff cannot be assigned to a project
            if ($emp && $emp->employment_type === 'office_staff') {
                return back()->withErrors([
                    'employees' => $emp->full_name . ' is office staff and cannot be assigned to field projects.'
                ]);
            }

            $syncData[$empId] = [
                'role_in_project' => $roles[$empId] ?? null,
                'assigned_at'     => now(),
            ];
        }

        if ($project->isDirty('location')) {
            Cache::forget("weather_{$project->latitude}_{$project->longitude}");
        }

        $project->users()->sync($syncData);

        // ----------------------------------------
        // 4. DONE
        // ----------------------------------------
        return redirect()
            ->route('projects.show', $project->id)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Delete project
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /* ==========================================================
     |  EXTRA WORK ACTIONS
     ========================================================== */

    /**
     * Store a new extra work line for a project.
     */
    public function storeExtraWork(Request $request, Project $project)
    {
        $data = $request->validate([
            'description'   => 'required|string|max:255',
            'volume_bdft'   => 'nullable|numeric|min:0',
            'rate_per_bdft' => 'nullable|numeric|min:0',
            'amount'        => 'nullable|numeric|min:0',
        ]);

        // If amount not manually provided, compute from bd.ft * rate
        if (($data['amount'] ?? null) === null) {
            $vol  = $data['volume_bdft']   ?? 0;
            $rate = $data['rate_per_bdft'] ?? 0;
            $data['amount'] = $vol * $rate;
        }

        $data['project_id'] = $project->id;
        $data['added_by']   = auth()->id();

        ProjectExtraWork::create($data);

        return back()->with('success', 'Extra work added to project.');
    }

    /**
     * Delete an extra work line.
     */
    public function destroyExtraWork(Project $project, ProjectExtraWork $extraWork)
    {
        // Safety: ensure it belongs to this project
        if ($extraWork->project_id !== $project->id) {
            abort(404);
        }

        $extraWork->delete();

        return back()->with('success', 'Extra work entry removed.');
    }


}
