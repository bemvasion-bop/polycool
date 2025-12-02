<?php

namespace App\Policies;

use App\Models\User;

class AttendanceScannerPolicy
{
    public function access(User $user): bool
    {
        return in_array($user->system_role, [
            'owner', 'manager', 'accounting'
        ]);
    }
}
