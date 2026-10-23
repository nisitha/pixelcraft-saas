<?php

namespace App\Filament\App\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Expense Information')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('invoice_id')
                                ->label('Linked Project / Invoice')
                                ->relationship(
                                    name: 'invoice',
                                    titleAttribute: 'invoice_number',
                                    modifyQueryUsing: fn ($query) => $query->with('contact')->latest()
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->invoice_number}".($record->contact ? " - {$record->contact->name}" : ''))
                                ->searchable(['invoice_number'])
                                ->preload()
                                ->nullable()
                                ->placeholder('Select linked invoice/project (optional)')
                                ->columnSpanFull(),
                            Select::make('category')
                                ->options([
                                    'Operations' => 'Operations',
                                    'Logistics & Transport' => 'Logistics & Transport',
                                    'Hardware & Tools' => 'Hardware & Tools',
                                    'Labor & Contractor' => 'Labor & Contractor',
                                    'Utilities & Rent' => 'Utilities & Rent',
                                    'Marketing' => 'Marketing',
                                    'Other' => 'Other',
                                ])
                                ->required(),
                            TextInput::make('title')
                                ->required(),
                            TextInput::make('amount')
                                ->numeric()
                                ->prefix('Rs.')
                                ->required()
                                ->default(0.00),
                            DatePicker::make('expense_date')
                                ->default(now())
                                ->required(),
                            TextInput::make('reference_no')
                                ->label('Receipt / Voucher Ref'),
                            Toggle::make('is_internal_overhead')
                                ->label('Internal Company Overhead Allocation')
                                ->helperText('Office/admin overhead absorbed internally. Retained inside company gross profit pool instead of third-party vendor outflow.')
                                ->default(false)
                                ->columnSpanFull(),
                            Textarea::make('notes')
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
