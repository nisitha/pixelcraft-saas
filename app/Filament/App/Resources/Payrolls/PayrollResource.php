<?php

namespace App\Filament\App\Resources\Payrolls;

use App\Filament\App\Resources\Payrolls\Pages\CreatePayroll;
use App\Filament\App\Resources\Payrolls\Pages\EditPayroll;
use App\Filament\App\Resources\Payrolls\Pages\ListPayrolls;
use App\Filament\App\Resources\Payrolls\RelationManagers\PayrollItemsRelationManager;
use App\Filament\App\Resources\Payrolls\Schemas\PayrollForm;
use App\Filament\App\Resources\Payrolls\Tables\PayrollsTable;
use App\Models\Payroll;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string|UnitEnum|null $navigationGroup = 'Payroll & HR';

    protected static ?string $navigationLabel = 'Monthly Payroll';

    protected static ?string $modelLabel = 'Payroll Run';

    protected static ?string $pluralModelLabel = 'Monthly Payroll Runs';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return PayrollForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayrollsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PayrollItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrolls::route('/'),
            'create' => CreatePayroll::route('/create'),
            'edit' => EditPayroll::route('/{record}/edit'),
        ];
    }
}

if (! class_exists(\App\Filament\App\Resources\PayrollResource::class, false)) {
    class_alias(PayrollResource::class, \App\Filament\App\Resources\PayrollResource::class);
}
