@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-8 shadow rounded">

    <h2 class="text-2xl font-semibold mb-6">Edit Project</h2>

    <form action="{{ route('projects.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ========================= --}}
        {{-- PROJECT DETAILS           --}}
        {{-- ========================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- LOCATION --}}
            <div>
                <label class="font-medium">Location</label>
                <input type="text" name="location"
                       value="{{ old('location', $project->location) }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- START DATE --}}
            <div>
                <label class="font-medium">Start Date</label>
                <input type="date"
                    name="start_date"
                    value="{{ $project->start_date ? date('Y-m-d', strtotime($project->start_date)) : '' }}"
                    class="w-full border rounded p-2">
            </div>

            {{-- END DATE --}}
            <div>
                <label class="font-medium">End Date</label>
                <input type="date"
                    name="end_date"
                    value="{{ $project->end_date ? date('Y-m-d', strtotime($project->end_date)) : '' }}"
                    class="w-full border rounded p-2">
            </div>

            {{-- STATUS --}}
            <div>
                <label class="font-medium">Status (Auto-calculated)</label>

                <input type="text"
                       value="{{ ucfirst($project->status) }}"
                       class="w-full border rounded p-2 bg-gray-100 text-gray-600"
                       disabled>

                <input type="hidden" name="status" value="{{ $project->status }}">
            </div>

        </div>


        {{-- ========================= --}}
        {{-- ASSIGNED WORKFORCE TABLE --}}
        {{-- ========================= --}}
        <div class="mt-10">
            <h3 class="text-xl font-semibold mb-3">Assigned Workforce</h3>

            <table class="w-full border mt-3">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border">Employee</th>
                        <th class="p-3 border">Role</th>
                        <th class="p-3 border">Actions</th>
                    </tr>
                </thead>

                <tbody id="assignedWorkforceBody"></tbody>
            </table>

            {{-- ADD BUTTON --}}
            <button type="button"
                    onclick="openAddWorkforceModal()"
                    class="mt-4 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                + Add Workforce
            </button>
        </div>


        {{-- ========================= --}}
        {{-- SAVE + BACK BUTTONS      --}}
        {{-- ========================= --}}
        <div class="mt-8 flex space-x-3">
            <button class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">
                Save Changes
            </button>

            <a href="{{ route('projects.show', $project->id) }}"
               class="px-6 py-3 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                Back
            </a>
        </div>

    </form>
</div>


{{-- ========================================================= --}}
{{-- ADD WORKFORCE MODAL                                      --}}
{{-- ========================================================= --}}
<div id="addWorkforceModal"
    class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center">

    <div class="bg-white p-6 rounded shadow-lg w-96">

        <h3 class="text-lg font-semibold mb-4">Assign Employee</h3>

        <label class="block mb-2">Employee:</label>
        <select id="wf_employee" class="border rounded w-full p-2 mb-4">
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
            @endforeach
        </select>

        <label class="block mb-2">Role in Project:</label>
        <select id="wf_role" class="border rounded w-full p-2 mb-4">
            <option value="Spray Operator">Spray Operator</option>
            <option value="Technician">Technician</option>
            <option value="Helper 1">Helper 1</option>
            <option value="Helper 2">Helper 2</option>
        </select>

        <button type="button"
                onclick="addWorkforceEntry()"
                class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
            Add
        </button>

        <button type="button"
                onclick="closeAddWorkforceModal()"
                class="w-full mt-2 bg-gray-300 py-2 rounded">
            Cancel
        </button>
    </div>
</div>


{{-- ========================================================= --}}
{{-- EDIT WORKFORCE MODAL                                     --}}
{{-- ========================================================= --}}
<div id="editWorkforceModal"
    class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center">

    <div class="bg-white p-6 rounded shadow-lg w-96">

        <h3 class="text-lg font-semibold mb-4">Edit Workforce Role</h3>

        <input type="hidden" id="edit_emp_id">

        <label class="block mb-2 font-medium">Employee:</label>
        <input type="text" id="edit_emp_name"
               class="border rounded w-full p-2 mb-4 bg-gray-100" disabled>

        <label class="block mb-2 font-medium">Role in Project:</label>
        <select id="edit_emp_role" class="border rounded w-full p-2 mb-4">
            @foreach($projectRoles as $role)
                <option value="{{ $role }}">{{ $role }}</option>
            @endforeach
        </select>

        <button type="button"
                onclick="saveEditWorkforce()"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Save Changes
        </button>

        <button type="button"
                onclick="closeEditModal()"
                class="w-full mt-2 bg-gray-300 py-2 rounded">
            Cancel
        </button>
    </div>
</div>



{{-- ========================================================= --}}
{{-- UPDATED WORKFORCE JAVASCRIPT (FULL WORKING)              --}}
{{-- ========================================================= --}}
<script>

    /* ========================================
     * LOAD EXISTING WORKFORCE FROM BACKEND
     * ======================================== */
    let workforce = @json(
        $project->users->map(function($u) {
            return [
                "id"   => $u->id,
                "name" => $u->full_name,
                "role" => $u->pivot->role_in_project
            ];
        })
    );


    /* ========================================
     * MODAL OPEN / CLOSE
     * ======================================== */
    function openAddWorkforceModal() {
        document.getElementById('addWorkforceModal').classList.remove('hidden');
    }

    function closeAddWorkforceModal() {
        document.getElementById('addWorkforceModal').classList.add('hidden');
    }

    function closeEditModal() {
        document.getElementById('editWorkforceModal').classList.add('hidden');
    }


    /* ========================================
     * ADD NEW WORKFORCE ENTRY
     * ======================================== */
    function addWorkforceEntry() {

        let empSel  = document.getElementById('wf_employee');
        let roleSel = document.getElementById('wf_role');

        let empId   = empSel.value;
        let empName = empSel.options[empSel.selectedIndex].text;
        let role    = roleSel.value;

        // Prevent duplicate
        if (workforce.some(e => e.id == empId)) {
            alert("Employee already assigned to this project.");
            return;
        }

        workforce.push({
            id: empId,
            name: empName,
            role: role
        });

        renderWorkforceTable();
        closeAddWorkforceModal();
    }


    /* ========================================
     * EDIT WORKFORCE ENTRY
     * ======================================== */
    function openEditModal(id) {
        let emp = workforce.find(e => e.id == id);

        document.getElementById('edit_emp_id').value = emp.id;
        document.getElementById('edit_emp_name').value = emp.name;
        document.getElementById('edit_emp_role').value = emp.role;

        document.getElementById('editWorkforceModal').classList.remove('hidden');
    }

    function saveEditWorkforce() {
        let id = document.getElementById('edit_emp_id').value;
        let newRole = document.getElementById('edit_emp_role').value;

        let emp = workforce.find(e => e.id == id);
        emp.role = newRole;

        renderWorkforceTable();
        closeEditModal();
    }


    /* ========================================
     * REMOVE WORKFORCE ENTRY
     * ======================================== */
    function removeWorkforceEntry(id) {
        workforce = workforce.filter(e => e.id != id);
        renderWorkforceTable();
    }


    /* ========================================
     * RENDER WORKFORCE TABLE + HIDDEN INPUTS
     * ======================================== */
    function renderWorkforceTable() {

        let tbody = document.getElementById('assignedWorkforceBody');
        tbody.innerHTML = "";

        workforce.forEach(emp => {

            tbody.innerHTML += `
                <tr class="border-b" data-id="${emp.id}">
                    <td class="p-3 border">${emp.name}</td>
                    <td class="p-3 border">${emp.role}</td>

                    <td class="p-3 border space-x-3">
                        <button type="button"
                            class="text-blue-600 hover:underline"
                            onclick="openEditModal(${emp.id})">
                            Edit
                        </button>

                        <button type="button"
                            class="text-red-600 hover:underline"
                            onclick="removeWorkforceEntry(${emp.id})">
                            Remove
                        </button>
                    </td>
                </tr>

                <!-- HIDDEN INPUTS SENT TO CONTROLLER -->
                <input type="hidden" name="employees[]" value="${emp.id}">
                <input type="hidden" name="roles[${emp.id}]" value="${emp.role}">
            `;
        });
    }


    /* ========================================
     * INITIAL RENDER
     * ======================================== */
    renderWorkforceTable();

</script>


@endsection
