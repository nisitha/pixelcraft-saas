<?php

namespace App\Policies;

use App\Models\SalesAgent;
use App\Models\User;

class SalesAgentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin() || $user->isTenantSalesExecutive();
    }

    public function view(User $user, SalesAgent $salesAgent): bool
    {
        return $user->isTenantOwnerOrAdmin() || $user->isTenantSalesExecutive();
    }

    public function create(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin() || $user->isTenantSalesExecutive();
    }

    public function update(User $user, SalesAgent $salesAgent): bool
    {
        return $user->isTenantOwnerOrAdmin() || $user->isTenantSalesExecutive();
    }

    public function delete(User $user, SalesAgent $salesAgent): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }
}
