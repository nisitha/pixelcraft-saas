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
                Section::make('Salary Advance Request')
                    ->description('Record and approve advance salary requests for employees')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->label('Employee')
                                ->relationship('employee', 'name')
                                ->searchable(['name', 'calling_name'])
                                ->preload()
                                ->required(),

                            TextInput::make('amount')
                                ->label('Advance Amount')
                                ->numeric()
                                ->prefix('Rs.')
                                ->required(),

                            DatePicker::make('request_date')
                                ->label('Request Date')
                                ->default(now())
                                ->required(),

                            TextInput::make('deduct_month')
                                ->label('Target Deduction Month')
                                ->placeholder('YYYY-MM (e.g. 2026-10)')
                                ->default(fn () => now()->format('Y-m'))
                                ->regex('/^\d{4}-\d{2}$/')
                                ->helperText('Month when this advance will be deducted from payroll (Format: YYYY-MM)')
                                ->required(),

                            Select::make('status')
                                ->label('Approval Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'approved' => 'Approved',
                                    'deducted' => 'Deducted',
                                    'rejected' => 'Rejected',
                                ])
                                ->default('pending')
                                ->required(),

                            Textarea::make('reason')
                                ->label('Reason for Request')
                                ->placeholder('State the purpose or justification for the advance request')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
