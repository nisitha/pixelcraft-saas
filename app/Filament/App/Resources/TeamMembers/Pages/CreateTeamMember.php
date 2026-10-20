<?php

namespace App\Filament\App\Resources\TeamMembers\Pages;

use App\Filament\App\Resources\TeamMembers\TeamMemberResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreateTeamMember extends CreateRecord
{
    protected static string $resource = TeamMemberResource::class;

    protected function beforeCreate(): void
    {
        $tenant = Filament::getTenant();

        if (! $tenant->canAddMoreUsers()) {
            $limit = $tenant->getMaxUsersLimit();
            Notification::make()
                ->title('User Limit Reached')
                ->body("Your current subscription plan allows a maximum of {$limit} team members. Please upgrade your plan in Subscription Billing to add more staff.")
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $tenant = Filament::getTenant();
        $email = strtolower(trim($data['email']));
        $role = $data['role'] ?? 'sales_executive';

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            if ($existingUser->companies()->where('companies.id', $tenant->id)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This user is already a team member of your company.',
                ]);
            }

            $existingUser->update([
                'name' => $data['name'] ?? $existingUser->name,
                'is_active' => $data['is_active'] ?? $existingUser->is_active,
            ]);

            if (! empty($data['password'])) {
                $existingUser->update(['password' => Hash::make($data['password'])]);
            }

            $tenant->users()->attach($existingUser->id, ['role' => $role]);

            return $existingUser;
        }

        $user = User::create([
            'name' => trim($data['name']),
            'email' => $email,
            'password' => Hash::make($data['password']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_super_admin' => false,
        ]);

        $tenant->users()->attach($user->id, ['role' => $role]);

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
