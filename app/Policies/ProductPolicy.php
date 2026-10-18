<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return ! $user->isTenantCashier();
    }

    public function update(User $user, Product $product): bool
    {
        return ! $user->isTenantCashier();
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }
}
