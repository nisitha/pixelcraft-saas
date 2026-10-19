<?php

namespace App\Filament\App\Resources\SalesAgents;

use App\Filament\App\Resources\SalesAgents\Pages\CreateSalesAgent;
use App\Filament\App\Resources\SalesAgents\Pages\EditSalesAgent;
use App\Filament\App\Resources\SalesAgents\Pages\ListSalesAgents;
use App\Filament\App\Resources\SalesAgents\Schemas\SalesAgentForm;
use App\Filament\App\Resources\SalesAgents\Tables\SalesAgentsTable;
use App\Models\SalesAgent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalesAgentResource extends Resource
{
    protected static ?string $model = SalesAgent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Sales Agents / Referrers';

    protected static ?string $modelLabel = 'Sales Agent / Referrer';

    protected static ?string $pluralModelLabel = 'Sales Agents & Referrers';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return SalesAgentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesAgentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalesAgents::route('/'),
            'create' => CreateSalesAgent::route('/create'),
            'edit' => EditSalesAgent::route('/{record}/edit'),
        ];
    }
}
