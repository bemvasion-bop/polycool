<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->system_role, ['owner','manager','audit','accounting']);
    }

    public function view(User $user, Project $project)
    {
        // Owners & Accounting & Managers: full access
        if (in_array($user->system_role, ['owner', 'manager', 'accounting'])) {
            return true;
        }

        // Employees: only if assigned to project
        return $project->users->contains($user->id);
    }

    public function create(User $user)
    {
        return in_array($user->system_role, ['owner','manager']);
    }

    public function update(User $user)
    {
        return in_array($user->system_role, ['owner','manager']);
    }

    public function delete(User $user)
    {
        return $user->system_role === 'owner';
    }
}
