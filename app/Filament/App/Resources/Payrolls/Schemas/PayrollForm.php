<?php

namespace App\Filament\App\Resources\Payrolls\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class PayrollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.app.components.payroll-kpi-banner')
                    ->columnSpanFull(),

                Section::make('Payroll Run Details & Identification')
                    ->description('Batch identification and processing workflow status')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
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

                Hidden::make('total_basic'),
                Hidden::make('total_allowances'),
                Hidden::make('total_gross'),
                Hidden::make('total_epf_employee'),
                Hidden::make('total_epf_employer'),
                Hidden::make('total_etf_employer'),
                Hidden::make('total_advances'),
                Hidden::make('total_deductions'),
                Hidden::make('total_net_payable'),
            ]);
    }
}
