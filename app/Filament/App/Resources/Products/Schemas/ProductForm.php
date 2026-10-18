<?php

namespace App\Filament\App\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product / Service Information')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')
                                ->required()
                                ->columnSpan(2),
                            TextInput::make('sku')
                                ->label('SKU / Item Code'),
                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('type')
                                ->options([
                                    'product' => 'Physical Product',
                                    'service' => 'Installation / Service',
                                    'solar_package' => 'Complete Solar Package',
                                ])
                                ->required()
                                ->default('product'),
                            TextInput::make('warranty_months')
                                ->label('Warranty (Months)')
                                ->numeric()
                                ->default(0),
                        ]),
                    ]),

                Section::make('Pricing & Inventory')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('unit_price')
                                ->label('Selling Price')
                                ->required()
                                ->numeric()
                                ->prefix('Rs.')
                                ->default(0.00),
                            TextInput::make('cost_price')
                                ->label('Cost Price')
                                ->required()
                                ->numeric()
                                ->prefix('Rs.')
                                ->default(0.00),
                            TextInput::make('stock_qty')
                                ->label('Current Stock Qty')
                                ->required()
                                ->numeric()
                                ->default(0),
                            TextInput::make('alert_qty')
                                ->label('Low Stock Alert Qty')
                                ->required()
                                ->numeric()
                                ->default(5),
                            Toggle::make('track_serial')
                                ->label('Track Serial Numbers (Inverter/Panels)')
                                ->default(false)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
