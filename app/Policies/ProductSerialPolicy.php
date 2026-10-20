<?php

namespace App\Policies;

use App\Models\ProductSerial;
use App\Models\User;

class ProductSerialPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ProductSerial $productSerial): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin() || $user->isTenantEngineer();
    }

    public function update(User $user, ProductSerial $productSerial): bool
    {
        return $user->isTenantOwnerOrAdmin() || $user->isTenantEngineer();
    }

    public function delete(User $user, ProductSerial $productSerial): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }
}
