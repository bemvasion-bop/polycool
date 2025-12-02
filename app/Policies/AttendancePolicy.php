<?php

namespace App\Policies;

use App\Models\User;

class AttendancePolicy
{
    // Anyone logged in can see their own logs
    public function viewSelf(User $user)
    {
        return $user->system_role === 'employee';
    }

    // Manager + owner can view/manage attendance
    public function manage(User $user)
    {
        return in_array($user->system_role, ['owner', 'manager']);
    }

    // Scanner access (Manager + owner)
    public function useScanner(User $user)
    {
        return in_array($user->system_role, ['owner', 'manager']);
    }

    // Edit logs manually — owner only
    public function manualEdit(User $user)
    {
        return $user->system_role === 'owner';
    }
}
