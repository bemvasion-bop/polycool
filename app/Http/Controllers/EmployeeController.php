<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('system_role', '!=', 'owner')
                 ->orderBy('given_name')
                 ->get();

        return view('employees.index', compact('employees'));
    }

    public function create()
    {

        return view('employees.create');
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'given_name'       => 'required|string|max:255',
            'middle_name'      => 'nullable|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'phone_number'     => 'nullable|string|max:255',
            'gender'           => 'nullable|string|max:20',
            'date_of_birth'    => 'nullable|date',
            'date_hired'       => 'nullable|date',
            'street_address'   => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'province'         => 'nullable|string|max:255',
            'postal_code'      => 'nullable|string|max:20',

            // System roles in your system
            'system_role'      => 'required|in:manager,employee,accounting,audit',

            // NEW FIELD (required)
            'employment_type'  => 'required|in:field_worker,office_staff',

            'employee_status'  => 'required|in:active,inactive',
            'password'         => 'required|confirmed|min:12',
        ]);

        // Create the user
        $user = User::create([
            'given_name'      => $request->given_name,
            'middle_name'     => $request->middle_name,
            'last_name'       => $request->last_name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'gender'          => $request->gender,
            'date_of_birth'   => $request->date_of_birth,
            'date_hired'      => $request->date_hired,
            'street_address'  => $request->street_address,
            'city'            => $request->city,
            'province'        => $request->province,
            'postal_code'     => $request->postal_code,

            'system_role'     => $request->system_role,

            // SAVE employment type
            'employment_type' => $request->employment_type,

            'employee_status' => $request->employee_status,
            'password'        => Hash::make($request->password),
        ]);

        // Generate unique QR code
        $user->qr_code = 'EMP-' . $user->id . '-' . strtoupper(Str::random(6));
        $user->save();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee created successfully!');
    }



    public function profile()
    {
        $user = Auth::user();

        return view('employee.profile-settings', compact('user'));
    }



    public function edit(User $employee)
    {
        if ($employee->system_role === 'owner') {
            abort(403, 'System Administrator cannot be modified.');
        }
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {

        if ($employee->system_role === 'owner') {
            abort(403, 'System Administrator cannot be modified.');
        }


        $validated = $request->validate([
            'given_name'       => 'required|string|max:255',
            'middle_name'      => 'nullable|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $employee->id,
            'phone_number'     => 'nullable|string|max:255',
            'gender'           => 'nullable|string|max:20',
            'date_of_birth'    => 'nullable|date',
            'date_hired'       => 'nullable|date',
            'street_address'   => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'province'         => 'nullable|string|max:255',
            'postal_code'      => 'nullable|string|max:20',
            'system_role'      => 'required|in:manager,employee,accounting,audit',
            'employee_status'  => 'required|in:active,inactive',
        ]);

        $employee->update([
            'given_name'      => $request->given_name,
            'middle_name'     => $request->middle_name,
            'last_name'       => $request->last_name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'gender'          => $request->gender,
            'date_of_birth'   => $request->date_of_birth,
            'date_hired'      => $request->date_hired,
            'street_address'  => $request->street_address,
            'city'            => $request->city,
            'province'        => $request->province,
            'postal_code'     => $request->postal_code,
            'system_role'     => $request->system_role,
            'employee_status' => $request->employee_status,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully!');
    }

    public function destroy(User $employee)
    {
        if ($employee->system_role === 'owner') {
            abort(403, 'System Administrator cannot be modified.');
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully!');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // 🔐 POLICY CHECK (THIS LINE CAUSES THE 403)
        $this->authorize('update', $user);

        $request->validate([
            'password' => 'nullable|confirmed|min:6',
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        dd(auth()->user()->system_role, auth()->user()->id);

        return back()->with('success', 'Password updated successfully.');
    }

}
