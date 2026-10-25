<?php

namespace App\Filament\App\Resources\Employees;

use App\Filament\App\Resources\Employees\Pages\CreateEmployee;
use App\Filament\App\Resources\Employees\Pages\EditEmployee;
use App\Filament\App\Resources\Employees\Pages\ListEmployees;
use App\Filament\App\Resources\Employees\Schemas\EmployeeForm;
use App\Filament\App\Resources\Employees\Tables\EmployeesTable;
use App\Models\Employee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Payroll & HR';

    protected static ?string $navigationLabel = 'Employees';

    protected static ?string $modelLabel = 'Employee';

    protected static ?string $pluralModelLabel = 'Employees';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table);
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
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}

if (! class_exists(\App\Filament\App\Resources\EmployeeResource::class, false)) {
    class_alias(EmployeeResource::class, \App\Filament\App\Resources\EmployeeResource::class);
}
