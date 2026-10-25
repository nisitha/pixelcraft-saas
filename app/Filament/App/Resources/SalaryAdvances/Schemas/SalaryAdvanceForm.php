<?php

namespace App\Filament\App\Resources\SalaryAdvances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalaryAdvanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Salary Advance Application & Approval')
                    ->description('Request, review, and schedule advance wage deductions for staff members')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->label('Employee Staff Member')
                                ->relationship('employee', 'name')
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} — {$record->designation} (Basic: Rs. ".number_format($record->basic_salary, 2).')')
                                ->searchable(['name', 'calling_name'])
                                ->preload()
                                ->required(),

                            TextInput::make('amount')
                                ->label('Advance Amount Requested')
                                ->numeric()
                                ->prefix('Rs.')
                                ->placeholder('e.g. 15000')
                                ->required(),

                            DatePicker::make('request_date')
                                ->label('Application Date')
                                ->default(now())
                                ->required(),

                            TextInput::make('deduct_month')
                                ->label('Target Deduction Month')
                                ->placeholder('YYYY-MM (e.g. 2026-10)')
                                ->default(fn () => now()->format('Y-m'))
                                ->regex('/^\d{4}-\d{2}$/')
                                ->helperText('Scheduled deduction month from monthly payroll run (Format: YYYY-MM)')
                                ->required(),

                            Select::make('status')
                                ->label('Review & Approval Status')
                                ->options([
                                    'pending' => 'Pending Review',
                                    'approved' => 'Approved (Ready to Deduct)',
                                    'deducted' => 'Deducted (Recovered in Payroll)',
                                    'rejected' => 'Rejected / Cancelled',
                                ])
                                ->default('pending')
                                ->required(),

                            Textarea::make('reason')
                                ->label('Purpose / Reason for Advance')
                                ->placeholder('State emergency medical, festival advance, tool purchase, or other reason')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
