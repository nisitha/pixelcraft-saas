<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\Quotations\QuotationResource;
use App\Models\Quotation;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSolarQuotationsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Solar PV Quotations')
            ->query(
                Quotation::query()
                    ->where('company_id', Filament::getTenant()?->id)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('quote_number')
                    ->label('Quote #')
                    ->weight('bold')
                    ->url(fn (Quotation $record): string => QuotationResource::getUrl('edit', ['record' => $record->id])),
                TextColumn::make('contact.name')
                    ->label('Customer'),
                TextColumn::make('system_kw')
                    ->label('Capacity')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kWp'),
                TextColumn::make('phase_type')
                    ->label('Phase')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'three' ? 'warning' : 'gray')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state).' Phase'),
                TextColumn::make('grand_total')
                    ->label('Total (LKR)')
                    ->money('LKR')
                    ->weight('bold'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'invoiced' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('quote_date')
                    ->label('Date')
                    ->date('d M Y'),
            ])
            ->paginated(false);
    }
}
