<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentInvoicesWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Invoices & Billing')
            ->query(
                Invoice::query()
                    ->where('company_id', Filament::getTenant()?->id)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->weight('bold')
                    ->url(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record->id])),
                TextColumn::make('contact.name')
                    ->label('Customer'),
                TextColumn::make('grand_total')
                    ->label('Total (LKR)')
                    ->money('LKR')
                    ->weight('bold'),
                TextColumn::make('due_amount')
                    ->label('Balance Due')
                    ->money('LKR')
                    ->color(fn ($state): string => (float) $state > 0 ? 'warning' : 'success'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('invoice_date')
                    ->label('Date')
                    ->date('d M Y'),
            ])
            ->paginated(false);
    }
}
