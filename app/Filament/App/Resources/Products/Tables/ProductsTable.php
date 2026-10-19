<?php

namespace App\Filament\App\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        $inventoryEnabled = (bool) (Filament::getTenant()?->settings['inventory_enabled'] ?? true);

        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('unit_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('stock_qty')
                    ->numeric()
                    ->sortable()
                    ->visible($inventoryEnabled),
                TextColumn::make('alert_qty')
                    ->numeric()
                    ->sortable()
                    ->visible($inventoryEnabled),
                IconColumn::make('track_inventory')
                    ->label('Track Stock')
                    ->boolean()
                    ->toggleable()
                    ->visible($inventoryEnabled),
                IconColumn::make('track_serial')
                    ->boolean()
                    ->visible($inventoryEnabled),
                TextColumn::make('warranty_months')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
