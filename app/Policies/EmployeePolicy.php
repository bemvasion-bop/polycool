<?php

namespace App\Policies;

use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->system_role, ['owner']);
    }

    public function view(User $user)
    {
        return in_array($user->system_role, ['owner']);
    }

    public function create(User $user)
    {
        return $user->system_role === 'owner';
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
