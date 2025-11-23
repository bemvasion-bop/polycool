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
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated($request, $user)
    {
        return match($user->role) {
            'owner'     => redirect()->route('owner.dashboard'),
            'manager'   => redirect()->route('manager.dashboard'),
            'employee'  => redirect()->route('employee.dashboard'),
            default     => abort(403, 'Unauthorized'),
        };
    }
}
