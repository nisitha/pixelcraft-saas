<?php

namespace App\Filament\App\Resources\Payments\Tables;

use App\Models\Payment;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->weight('bold'),
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
                //
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
