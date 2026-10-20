<?php

namespace App\Filament\App\Resources\CommissionPayouts\Tables;

use App\Filament\App\Resources\CommissionPayouts\CommissionPayoutResource;
use App\Models\CommissionPayout;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommissionPayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (CommissionPayout $record): string => CommissionPayoutResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('voucher_number')
                    ->label('Voucher #')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('payout_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('salesAgent.name')
                    ->label('Sales Agent')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('invoice.invoice_number')
                    ->label('Linked Invoice')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Amount Disbursed')
                    ->money('LKR')
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Method')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->badge()
                    ->color('gray'),

                TextColumn::make('reference_no')
                    ->label('Ref / Cheque #')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'paid' ? 'success' : 'gray'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('downloadPdf')
                    ->label('Download Voucher')
                    ->icon(Heroicon::ArrowDownTray)
                    ->color('primary')
                    ->action(function (CommissionPayout $record) {
                        $filename = PdfService::getCommissionVoucherFileName($record);

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generateCommissionVoucherPdf($record)->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
                DeleteAction::make()
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
