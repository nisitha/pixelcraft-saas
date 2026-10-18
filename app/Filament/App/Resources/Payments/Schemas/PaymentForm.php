<?php

namespace App\Filament\App\Resources\Payments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Information')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('invoice_id')
                                ->label('Invoice')
                                ->relationship('invoice', 'invoice_number')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DatePicker::make('payment_date')
                                ->default(now())
                                ->required(),
                            TextInput::make('amount')
                                ->label('Paid Amount')
                                ->required()
                                ->numeric()
                                ->prefix('Rs.')
                                ->default(0.00),
                            Select::make('method')
                                ->options([
                                    'cash' => 'Cash',
                                    'bank_transfer' => 'Bank Transfer',
                                    'card' => 'Credit / Debit Card',
                                    'cheque' => 'Cheque',
                                ])
                                ->required()
                                ->default('cash'),
                            TextInput::make('reference_no')
                                ->label('Transaction / Cheque Reference'),
                            TextInput::make('milestone_label')
                                ->label('Milestone Stage Label')
                                ->placeholder('e.g. 50% Advance, 40% Delivery, 10% Grid Connection'),
                            Textarea::make('notes')
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
