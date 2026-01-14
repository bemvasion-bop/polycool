<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Employee can view OWN profile
     * Owner & Manager can view anyone
     */
    public function view(User $authUser, User $targetUser): bool
    {
        // Owner & Manager can view anyone
        if (in_array($authUser->system_role, ['owner'])) {
            return true;
        }

        // Accounting & Employee & Audit → own profile only
        return $authUser->id === $targetUser->id;
    }

    /**
     * Employee can update OWN profile
     */
    public function update(User $authUser, User $targetUser): bool
    {
        // Owner & Manager can update anyone
        if (in_array($authUser->system_role, ['owner'])) {
            return true;
        }

        // Accounting & Employee → own profile only
        if (in_array($authUser->system_role, ['employee', 'accounting', 'owner'])) {
            return $authUser->id === $targetUser->id;
        }

        // Audit → cannot update
        return false;
    }

}
