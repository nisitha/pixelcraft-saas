<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contact $contact): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contact $contact): bool
    {
        return true;
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isTenantOwnerOrAdmin();
    }
}
