<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated($request, $user)
    {
        $role = $user->system_role;   // <-- FIXED

        return match ($role) {
            'owner'      => redirect()->route('owner.dashboard'),
            'manager'    => redirect()->route('manager.dashboard'),
            'employee'   => redirect()->route('employee.dashboard'),
            'accounting' => redirect()->route('accounting.dashboard'),
            'audit'      => redirect()->route('audit.dashboard'),

            default      => abort(403, 'Unauthorized role'),
        };
    }

}
