<?php

if (!function_exists('role')) {
    function role($roles)
    {
        $user = auth()->user();
        if (!$user) return false;

        // Allow array or single string
        $roles = is_array($roles) ? $roles : explode(',', $roles);

        return in_array($user->system_role, $roles);
    }
}
