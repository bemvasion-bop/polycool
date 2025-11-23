<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    // List all non-owner users
    public function index()
    {
        $employees = User::whereIn('role', ['manager', 'employee'])
            ->orderBy('name')
            ->get();

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'given_name'        => 'required|string|max:255',
            'middle_name'       => 'nullable|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email',
            'phone_number'      => 'nullable|string|max:255',
            'gender'            => 'nullable|in:male,female,other',
            'date_of_birth'     => 'nullable|date',
            'date_hired'        => 'nullable|date',
            'street_address'    => 'nullable|string|max:255',
            'city'              => 'nullable|string|max:255',
            'province'          => 'nullable|string|max:255',
            'postal_code'       => 'nullable|string|max:255',
            'position_title'    => 'required|string|max:255',
            'employee_status'   => 'required|in:active,inactive',
            'system_role'       => 'required|in:manager,employee',
            'password'          => 'required|confirmed|min:6',
        ]);

        $name = trim(
            $data['given_name'].' '.
            ($data['middle_name'] ?? '').' '.
            $data['last_name']
        );

        $user = User::create([
            'name'            => $name,
            'email'           => $data['email'],
            'password'        => Hash::make($data['password']),
            'role'            => $data['system_role'],

            'given_name'      => $data['given_name'],
            'middle_name'     => $data['middle_name'] ?? null,
            'last_name'       => $data['last_name'],
            'phone_number'    => $data['phone_number'] ?? null,
            'gender'          => $data['gender'] ?? null,
            'date_of_birth'   => $data['date_of_birth'] ?? null,
            'date_hired'      => $data['date_hired'] ?? null,
            'street_address'  => $data['street_address'] ?? null,
            'city'            => $data['city'] ?? null,
            'province'        => $data['province'] ?? null,
            'postal_code'     => $data['postal_code'] ?? null,
            'position_title'  => $data['position_title'],
            'employee_status' => $data['employee_status'],
        ]);

        return redirect()
            ->route('employees.show', $user)
            ->with('success', 'Employee created successfully.');
    }

    public function show(User $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $data = $request->validate([
            'given_name'        => 'required|string|max:255',
            'middle_name'       => 'nullable|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email,'.$employee->id,
            'phone_number'      => 'nullable|string|max:255',
            'gender'            => 'nullable|in:male,female,other',
            'date_of_birth'     => 'nullable|date',
            'date_hired'        => 'nullable|date',
            'street_address'    => 'nullable|string|max:255',
            'city'              => 'nullable|string|max:255',
            'province'          => 'nullable|string|max:255',
            'postal_code'       => 'nullable|string|max:255',
            'position_title'    => 'required|string|max:255',
            'employee_status'   => 'required|in:active,inactive',
            'system_role'       => 'required|in:manager,employee',
            'password'          => 'nullable|confirmed|min:6',
        ]);

        $name = trim(
            $data['given_name'].' '.
            ($data['middle_name'] ?? '').' '.
            $data['last_name']
        );

        $employee->name            = $name;
        $employee->email           = $data['email'];
        $employee->role            = $data['system_role'];
        $employee->given_name      = $data['given_name'];
        $employee->middle_name     = $data['middle_name'] ?? null;
        $employee->last_name       = $data['last_name'];
        $employee->phone_number    = $data['phone_number'] ?? null;
        $employee->gender          = $data['gender'] ?? null;
        $employee->date_of_birth   = $data['date_of_birth'] ?? null;
        $employee->date_hired      = $data['date_hired'] ?? null;
        $employee->street_address  = $data['street_address'] ?? null;
        $employee->city            = $data['city'] ?? null;
        $employee->province        = $data['province'] ?? null;
        $employee->postal_code     = $data['postal_code'] ?? null;
        $employee->position_title  = $data['position_title'];
        $employee->employee_status = $data['employee_status'];

        if (!empty($data['password'])) {
            $employee->password = Hash::make($data['password']);
        }

        $employee->save();

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        // never allow deleting owner via this controller
        if ($employee->role === 'owner') {
            abort(403, 'Cannot delete owner account.');
        }

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
