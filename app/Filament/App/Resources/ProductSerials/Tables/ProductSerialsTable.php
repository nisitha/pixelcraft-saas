<?php

namespace App\Filament\App\Resources\ProductSerials\Tables;

use App\Models\ProductSerial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductSerialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serial_number')
                    ->label('Serial #')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_stock' => 'success',
                        'sold' => 'info',
                        'defective' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('contact.name')
                    ->label('Customer')
                    ->searchable()
                    ->placeholder('None (In Stock)'),
                TextColumn::make('contact.phone')
                    ->label('Customer Phone')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),
                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('warranty_months')
                    ->label('Warranty')
                    ->suffix(' mos')
                    ->sortable(),
                TextColumn::make('warranty_expires_at')
                    ->label('Warranty Expiry')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('warranty_status')
                    ->label('Warranty Status')
                    ->state(fn (ProductSerial $record): string => $record->isWarrantyActive() ? 'Valid' : ($record->warranty_expires_at ? 'Expired' : 'No Expiry'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Valid' => 'success',
                        'Expired' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'sold' => 'Sold / Installed',
                        'defective' => 'Defective / RMA',
                    ]),
                Filter::make('active_warranty')
                    ->label('Active Warranty Only')
                    ->query(fn (Builder $query) => $query->where('warranty_expires_at', '>=', now())),
                Filter::make('expired_warranty')
                    ->label('Expired Warranty')
                    ->query(fn (Builder $query) => $query->where('warranty_expires_at', '<', now())),
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
