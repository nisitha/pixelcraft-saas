<?php

namespace App\Filament\App\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Employee Profile')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Personal & Role Details')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Employee Identification & Role')
                                    ->description('Official identification, designation, and direct contact channels')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('name')
                                                ->label('Full Legal Name')
                                                ->placeholder('e.g. Kasun Chamara Perera')
                                                ->required()
                                                ->maxLength(255),

                                            TextInput::make('calling_name')
                                                ->label('Calling / Preferred Name')
                                                ->placeholder('e.g. Kasun')
                                                ->maxLength(255),

                                            TextInput::make('nic_number')
                                                ->label('National Identity Card (NIC)')
                                                ->placeholder('e.g. 199318402910 / 931840291V')
                                                ->helperText('Standard Sri Lankan NIC format (12-digit or 9-digit with V/X)')
                                                ->maxLength(20),

                                            Select::make('designation')
                                                ->label('Designation / Role')
                                                ->options([
                                                    'Solar Technician' => 'Solar Technician',
                                                    'Site Supervisor' => 'Site Supervisor',
                                                    'Electrician' => 'Electrician',
                                                    'Office Staff' => 'Office Staff',
                                                    'Sales Engineer' => 'Sales Engineer',
                                                    'Project Manager' => 'Project Manager',
                                                    'Accountant' => 'Accountant',
                                                    'Driver' => 'Driver',
                                                ])
                                                ->searchable()
                                                ->required(),

                                            TextInput::make('contact_number')
                                                ->label('Contact Mobile Number')
                                                ->tel()
                                                ->placeholder('077 123 4567')
                                                ->maxLength(50),

                                            DatePicker::make('joined_date')
                                                ->label('Joined / Appointment Date')
                                                ->default(now())
                                                ->required(),

                                            Toggle::make('is_active')
                                                ->label('Active Staff Status')
                                                ->helperText('Active employees are automatically loaded into monthly payroll runs')
                                                ->default(true)
                                                ->columnSpanFull(),
                                        ]),
                                    ]),
                            ]),

                        Tab::make('Remuneration & Statutory')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Monthly Remuneration Structure')
                                    ->description('Define agreed monthly salary and statutory EPF/ETF contribution parameters')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('basic_salary')
                                                ->label('Basic Salary (Monthly)')
                                                ->numeric()
                                                ->prefix('Rs.')
                                                ->default(0.00)
                                                ->live(debounce: 400)
                                                ->required(),

                                            TextInput::make('fixed_allowance')
                                                ->label('Fixed Monthly Allowance')
                                                ->numeric()
                                                ->prefix('Rs.')
                                                ->default(0.00)
                                                ->live(debounce: 400),

                                            TextInput::make('daily_rate')
                                                ->label('Agreed Daily Wage Rate (Optional)')
                                                ->numeric()
                                                ->prefix('Rs.')
                                                ->nullable()
                                                ->helperText('Reference rate for site laborers or attendance agreements'),

                                            TextInput::make('epf_number')
                                                ->label('EPF / ETF Registration Number')
                                                ->placeholder('e.g. EPF-10492')
                                                ->maxLength(50),
                                        ]),

                                        View::make('filament.app.components.employee-salary-preview')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Bank Remittance')
                            ->icon('heroicon-o-building-library')
                            ->schema([
                                Section::make('Direct Bank Transfer Remittance')
                                    ->description('Bank account information for automated monthly salary disbursement')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('bank_name')
                                                ->label('Bank Name')
                                                ->placeholder('e.g. Commercial Bank of Ceylon / Sampath Bank / BOC')
                                                ->maxLength(255),

                                            TextInput::make('bank_branch')
                                                ->label('Branch Name')
                                                ->placeholder('e.g. Mirigama Branch')
                                                ->maxLength(255),

                                            TextInput::make('bank_account_number')
                                                ->label('Account Number')
                                                ->placeholder('e.g. 8001928371')
                                                ->maxLength(50)
                                                ->columnSpanFull(),
                                        ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
