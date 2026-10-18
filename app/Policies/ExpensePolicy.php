<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return ! $user->isTenantCashier();
    }

    public function view(User $user, Expense $expense): bool
    {
        return ! $user->isTenantCashier();
    }

    public function create(User $user): bool
    {
        return ! $user->isTenantCashier();
    }

    public function update(User $user, Expense $expense): bool
    {
        return ! $user->isTenantCashier();
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }
}
