<?php

namespace App\Filament\App\Resources\Payments\Tables;

use App\Models\Payment;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('payment_date', 'desc')
            ->columns([
                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('invoice.contact.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payment_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('LKR')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('method')
                    ->label('Payment Method')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => strtoupper(str_replace('_', ' ', $state ?? 'CASH'))),
                TextColumn::make('reference_no')
                    ->label('Reference #')
                    ->searchable(),
                TextColumn::make('milestone_label')
                    ->label('Milestone / Purpose')
                    ->searchable(),
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
                Filter::make('payment_date')
                    ->form([
                        DatePicker::make('from')->label('Payment Date From'),
                        DatePicker::make('until')->label('Payment Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),

                SelectFilter::make('method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'pos' => 'Counter POS',
                        'card' => 'Credit/Debit Card',
                        'cheque' => 'Cheque',
                        'online' => 'Online Payment',
                    ]),
            ])
            ->recordActions([
                Action::make('downloadVoucher')
                    ->label('Receipt Voucher')
                    ->icon(Heroicon::DocumentCheck)
                    ->color('success')
                    ->action(function (Payment $record) {
                        $filename = PdfService::getPaymentReceiptFileName($record);

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generatePaymentReceiptPdf($record)->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
                ]),
            ]);
    }
}
