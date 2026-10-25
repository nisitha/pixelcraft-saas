<?php

namespace App\Filament\App\Resources\Payrolls\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PayrollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payroll Run Details & Status')
                    ->description('Monthly payroll batch information and processing state')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('month')
                                ->label('Payroll Month')
                                ->placeholder('YYYY-MM (e.g. 2026-10)')
                                ->disabled(fn ($record) => $record !== null)
                                ->required(),

                            TextInput::make('title')
                                ->label('Payroll Title')
                                ->required(),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'draft' => 'Draft',
                                    'confirmed' => 'Confirmed',
                                    'paid' => 'Paid',
                                ])
                                ->disabled(fn ($record) => $record?->isConfirmed() || $record?->isPaid())
                                ->required(),
                        ]),
                    ]),

                Section::make('Run KPI Summary & Statutory Remittance')
                    ->description('Aggregated financial summary of all included employees')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('total_basic')
                                ->label('Total Basic Salary')
                                ->numeric()
                                ->prefix('Rs.')
                                ->readOnly(),

                            TextInput::make('total_allowances')
                                ->label('Total Allowances')
                                ->numeric()
                                ->prefix('Rs.')
                                ->readOnly(),

                            TextInput::make('total_gross')
                                ->label('Total Gross Pay')
                                ->numeric()
                                ->prefix('Rs.')
                                ->readOnly(),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('total_epf_employee')
                                ->label('EPF Employee (8%)')
                                ->numeric()
                                ->prefix('Rs.')
                                ->readOnly(),

                            TextInput::make('total_epf_employer')
                                ->label('EPF Employer (12%)')
                                ->numeric()
                                ->prefix('Rs.')
                                ->readOnly(),

                            TextInput::make('total_etf_employer')
                                ->label('ETF Employer (3%)')
                                ->numeric()
                                ->prefix('Rs.')
                                ->readOnly(),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('total_advances')
                                ->label('Total Advances Deducted')
                                ->numeric()
                                ->prefix('Rs.')
                                ->readOnly(),

                            TextInput::make('total_deductions')
                                ->label('Total Deductions')
                                ->numeric()
                                ->prefix('Rs.')
                                ->readOnly(),

                            TextInput::make('total_net_payable')
                                ->label('Total Net Payable to Staff')
                                ->numeric()
                                ->prefix('Rs.')
                                ->extraAttributes(['class' => 'font-bold text-success-600'])
                                ->readOnly(),
                        ]),
                    ]),
            ]);
    }
}
