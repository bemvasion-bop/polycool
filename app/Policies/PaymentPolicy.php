<?php

namespace App\Policies;


use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->system_role, ['owner','accounting']);
    }

    public function create(User $user)
    {
        return in_array($user->system_role, ['owner','accounting']);
    }

    public function update(User $user)
    {
        return $user->system_role === 'owner';
    }

    public function delete(User $user)
    {
        return $user->system_role === 'owner';
    }

    public function cancel(User $user, Payment $payment)
    {
        return in_array($user->system_role, ['owner', 'accounting']);
    }

    public function reissue(User $user)
    {
        return in_array($user->system_role, ['owner', 'accounting']);
    }
}
