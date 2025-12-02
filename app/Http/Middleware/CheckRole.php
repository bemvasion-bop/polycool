<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage:
     *   ->middleware('role:owner')
     *   ->middleware('role:owner,manager')
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Normalize role name
        $normalizedRole = strtolower(trim($user->system_role));

        // Map aliases to real roles
        $roleMap = [
            'system administrator' => 'owner',
            'system_admin'         => 'owner',
            'admin'                => 'owner',
        ];

        // Convert to mapped role or keep original
        $effectiveRole = $roleMap[$normalizedRole] ?? $normalizedRole;

        // Check if the user role is allowed in this route group
        if (!in_array($effectiveRole, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
