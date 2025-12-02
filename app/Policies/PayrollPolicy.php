<?php

namespace App\Policies;

use App\Models\User;

class PayrollPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->system_role, ['owner','accounting']);
    }

    public function create(User $user)
    {
        return in_array($user->system_role, ['owner','accounting']);
    }
}
