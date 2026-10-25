<?php

namespace App\Filament\App\Resources\SalaryAdvances;

use App\Filament\App\Resources\SalaryAdvances\Pages\CreateSalaryAdvance;
use App\Filament\App\Resources\SalaryAdvances\Pages\EditSalaryAdvance;
use App\Filament\App\Resources\SalaryAdvances\Pages\ListSalaryAdvances;
use App\Filament\App\Resources\SalaryAdvances\Schemas\SalaryAdvanceForm;
use App\Filament\App\Resources\SalaryAdvances\Tables\SalaryAdvancesTable;
use App\Models\SalaryAdvance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SalaryAdvanceResource extends Resource
{
    protected static ?string $model = SalaryAdvance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Payroll & HR';

    protected static ?string $navigationLabel = 'Salary Advances';

    protected static ?string $modelLabel = 'Salary Advance';

    protected static ?string $pluralModelLabel = 'Salary Advances';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return SalaryAdvanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalaryAdvancesTable::configure($table);
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
            'index' => ListSalaryAdvances::route('/'),
            'create' => CreateSalaryAdvance::route('/create'),
            'edit' => EditSalaryAdvance::route('/{record}/edit'),
        ];
    }
}

if (! class_exists(\App\Filament\App\Resources\SalaryAdvanceResource::class, false)) {
    class_alias(SalaryAdvanceResource::class, \App\Filament\App\Resources\SalaryAdvanceResource::class);
}
