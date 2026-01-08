<?php

use App\Models\AuditLog;

if (!function_exists('role')) {
    function role($roles)
    {
        $user = auth()->user();
        if (!$user) return false;

        $roles = is_array($roles) ? $roles : explode(',', $roles);

        return in_array($user->system_role, $roles);
    }
}

if (!function_exists('audit_log')) {
    function audit_log($action, $details = null)
    {
        if (!auth()->check()) return;

        AuditLog::create([
            'action' => $action,
            'details' => $details,
            'performed_by' => auth()->id(),
        ]);
    }
}
