<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // NEW: Use system_role instead of role
        if (!in_array($user->system_role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
