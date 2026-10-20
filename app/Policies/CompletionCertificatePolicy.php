<?php

namespace App\Policies;

use App\Models\CompletionCertificate;
use App\Models\User;

class CompletionCertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return ! $user->isTenantCashier();
    }

    public function view(User $user, CompletionCertificate $certificate): bool
    {
        return ! $user->isTenantCashier();
    }

    public function create(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin() || $user->isTenantEngineer();
    }

    public function update(User $user, CompletionCertificate $certificate): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function delete(User $user, CompletionCertificate $certificate): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }
}
