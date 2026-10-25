<?php

namespace App\Filament\App\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'lg' => 2,
                ])->schema([
                    Section::make('Personal Details')
                        ->description('Basic employee identification, role, and contact information')
                        ->schema([
                            TextInput::make('name')
                                ->label('Full Name')
                                ->placeholder('e.g. Kasun Chamara Perera')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('calling_name')
                                ->label('Calling Name')
                                ->placeholder('e.g. Kasun')
                                ->maxLength(255),

                            TextInput::make('nic_number')
                                ->label('NIC Number')
                                ->placeholder('e.g. 199318402910 / 931840291V')
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
                                ->label('Contact Number')
                                ->tel()
                                ->placeholder('077 123 4567')
                                ->maxLength(50),

                            DatePicker::make('joined_date')
                                ->label('Joined Date')
                                ->default(now())
                                ->required(),

                            Toggle::make('is_active')
                                ->label('Active Employee')
                                ->helperText('Disable to mark the employee as resigned or inactive')
                                ->default(true),
                        ]),

                    Group::make()->schema([
                        Section::make('Salary & EPF Information')
                            ->description('Remuneration breakdown and statutory EPF/ETF contributions')
                            ->schema([
                                TextInput::make('basic_salary')
                                    ->label('Basic Salary')
                                    ->numeric()
                                    ->prefix('Rs.')
                                    ->default(0.00)
                                    ->required(),

                                TextInput::make('fixed_allowance')
                                    ->label('Fixed Allowance')
                                    ->numeric()
                                    ->prefix('Rs.')
                                    ->default(0.00),

                                TextInput::make('daily_rate')
                                    ->label('Daily Rate')
                                    ->numeric()
                                    ->prefix('Rs.')
                                    ->nullable()
                                    ->helperText('Applicable for day-wage / site labor contracts'),

                                TextInput::make('epf_number')
                                    ->label('EPF / ETF Number')
                                    ->placeholder('e.g. EPF-10492')
                                    ->maxLength(50),
                            ])->columns(2),

                        Section::make('Bank Transfer Information')
                            ->description('Bank remittance details for monthly salary transfer')
                            ->schema([
                                TextInput::make('bank_name')
                                    ->label('Bank Name')
                                    ->placeholder('e.g. Commercial Bank of Ceylon')
                                    ->maxLength(255),

                                TextInput::make('bank_branch')
                                    ->label('Branch')
                                    ->placeholder('e.g. Colombo Fort Branch')
                                    ->maxLength(255),

                                TextInput::make('bank_account_number')
                                    ->label('Account Number')
                                    ->placeholder('e.g. 8001928371')
                                    ->maxLength(50)
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ]),
                ]),
            ]);
    }
}
