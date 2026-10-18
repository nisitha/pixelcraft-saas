<?php

namespace App\Filament\App\Resources\ProductSerials\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductSerialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Serial & Warranty Information')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('product_id')
                                ->label('Product')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('serial_number')
                                ->label('Unique Serial Number')
                                ->placeholder('e.g. SN-JKM-585-009482')
                                ->required(),
                            Select::make('status')
                                ->options([
                                    'in_stock' => 'In Stock',
                                    'sold' => 'Sold / Installed',
                                    'defective' => 'Defective / RMA',
                                ])
                                ->default('in_stock')
                                ->required(),
                            Select::make('contact_id')
                                ->label('Customer / Client')
                                ->relationship('contact', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('invoice_id')
                                ->label('Originating Invoice')
                                ->relationship('invoice', 'invoice_number')
                                ->searchable()
                                ->preload(),
                            TextInput::make('warranty_months')
                                ->label('Warranty Period (Months)')
                                ->numeric()
                                ->default(12)
                                ->required(),
                            DatePicker::make('warranty_expires_at')
                                ->label('Warranty Expiry Date')
                                ->default(now()->addMonths(12)),
                            Textarea::make('notes')
                                ->label('Inspection / Warranty Notes')
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
