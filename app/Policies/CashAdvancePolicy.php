<?php

namespace App\Policies;

use App\Models\User;

class CashAdvancePolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->system_role, ['owner','accounting']);
    }

    public function create(User $user)
    {
        return $user->system_role === 'employee';
    }
}
