<?php

namespace App\Policies;

use App\Models\CommissionPayout;
use App\Models\User;

class CommissionPayoutPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin() || $user->isTenantSalesExecutive();
    }

    public function view(User $user, CommissionPayout $payout): bool
    {
        return $user->isTenantOwnerOrAdmin() || $user->isTenantSalesExecutive();
    }

    public function create(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function update(User $user, CommissionPayout $payout): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function delete(User $user, CommissionPayout $payout): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }
}
