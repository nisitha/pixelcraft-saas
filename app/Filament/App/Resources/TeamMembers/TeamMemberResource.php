<?php

namespace App\Filament\App\Resources\TeamMembers;

use App\Filament\App\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Filament\App\Resources\TeamMembers\Pages\EditTeamMember;
use App\Filament\App\Resources\TeamMembers\Pages\ListTeamMembers;
use App\Filament\App\Resources\TeamMembers\Schemas\TeamMemberForm;
use App\Filament\App\Resources\TeamMembers\Tables\TeamMembersTable;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeamMemberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'Team & Staff';

    protected static ?string $modelLabel = 'Staff Member';

    protected static ?string $pluralModelLabel = 'Team & Staff Members';

    protected static ?string $slug = 'team-members';

    protected static ?int $navigationSort = 9;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return (bool) $user?->isTenantOwnerOrAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('companies', fn ($q) => $q->where('companies.id', Filament::getTenant()->id));
    }

    public static function form(Schema $schema): Schema
    {
        return TeamMemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeamMembersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeamMembers::route('/'),
            'create' => CreateTeamMember::route('/create'),
            'edit' => EditTeamMember::route('/{record}/edit'),
        ];
    }
}
