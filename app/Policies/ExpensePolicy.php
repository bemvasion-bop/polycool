<?php

namespace App\Policies;

use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->system_role, ['owner','accounting']);
    }

    public function create(User $user)
    {
        return in_array($user->system_role, ['owner','manager']);
    }

    public function update(User $user)
    {
        return $user->system_role === 'owner';
    }

    public function delete(User $user)
    {
        return $user->system_role === 'owner';
    }
}
