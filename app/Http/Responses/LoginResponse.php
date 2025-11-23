<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        $redirect = match($user->role) {
            'owner' => '/dashboard',
            'manager' => '/manager',
            'employee' => '/employee',
            default => '/dashboard'
        };

        return redirect()->intended($redirect);
    }
}
