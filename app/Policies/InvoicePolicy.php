<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return ! $user->isTenantEngineer();
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }
}
