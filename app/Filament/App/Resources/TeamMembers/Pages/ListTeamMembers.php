<?php

namespace App\Filament\App\Resources\TeamMembers\Pages;

use App\Filament\App\Resources\TeamMembers\TeamMemberResource;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListTeamMembers extends ListRecords
{
    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        $tenant = Filament::getTenant();
        $currentUsersCount = $tenant?->users()->count() ?? 0;
        $maxUsers = $tenant?->getMaxUsersLimit() ?? 5;

        return [
            CreateAction::make()
                ->label('+ Add Team Member')
                ->tooltip("Plan Capacity: {$currentUsersCount} of {$maxUsers} team members used"),
        ];
    }
}
