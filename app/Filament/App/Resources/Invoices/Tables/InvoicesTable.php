<?php

namespace App\Filament\App\Resources\Invoices\Tables;

use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('contact.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'milestone' => 'warning',
                        'pos' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('grand_total')
                    ->label('Total')
                    ->money('LKR')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->money('LKR')
                    ->color('success')
                    ->sortable(),
                TextColumn::make('due_amount')
                    ->label('Balance Due')
                    ->money('LKR')
                    ->color(fn (float $state): string => $state > 0 ? 'danger' : 'gray')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'unpaid' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record])),

                Action::make('markAsSent')
                    ->label('Mark as Sent')
                    ->icon(Heroicon::CheckCircle)
                    ->color('info')
                    ->visible(fn (Invoice $record): bool => ! ($record->is_sent ?? false))
                    ->action(function (Invoice $record) {
                        $record->update([
                            'is_sent' => true,
                            'sent_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Invoice marked as Sent')
                            ->success()
                            ->send();
                    }),

                Action::make('unlock')
                    ->label('Unlock & Edit')
                    ->icon(Heroicon::LockOpen)
                    ->color('warning')
                    ->visible(fn (Invoice $record): bool => (bool) ($record->is_sent ?? false))
                    ->action(function (Invoice $record, Action $action) {
                        $record->update(['is_sent' => false]);

                        Notification::make()
                            ->title('Invoice unlocked for editing')
                            ->info()
                            ->send();

                        return $action->redirect(InvoiceResource::getUrl('edit', ['record' => $record]));
                    }),

                Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon(Heroicon::ArrowDownTray)
                    ->color('primary')
                    ->action(function (Invoice $record) {
                        $safeNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
                        $filename = "Invoice-{$safeNumber}.pdf";

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generateInvoicePdf($record)->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Action::make('downloadReceipt')
                    ->label('80mm POS')
                    ->icon(Heroicon::ReceiptPercent)
                    ->color('warning')
                    ->action(function (Invoice $record) {
                        $safeNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
                        $filename = "Receipt-{$safeNumber}.pdf";

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generatePosReceiptPdf($record)->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Action::make('deliveryNote')
                    ->label('Delivery Note')
                    ->icon(Heroicon::Truck)
                    ->color('gray')
                    ->action(function (Invoice $record) {
                        $filename = PdfService::getDeliveryNoteFileName($record);

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generateDeliveryNotePdf($record)->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Action::make('downloadReceiptVoucher')
                    ->label('Receipt Voucher')
                    ->icon(Heroicon::DocumentCheck)
                    ->color('success')
                    ->visible(fn (Invoice $record): bool => $record->payments()->count() > 0)
                    ->action(function (Invoice $record) {
                        $payment = $record->payments()->latest()->first();
                        if (! $payment) {
                            return null;
                        }

                        $filename = PdfService::getPaymentReceiptFileName($payment);

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generatePaymentReceiptPdf($payment)->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon(Heroicon::DocumentDuplicate)
                    ->color('gray')
                    ->action(function (Invoice $record, Action $action) {
                        $newInvoice = DB::transaction(function () use ($record) {
                            $newNumber = 'INV-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);

                            $invoice = Invoice::create([
                                'company_id' => $record->company_id,
                                'contact_id' => $record->contact_id,
                                'quotation_id' => $record->quotation_id,
                                'invoice_number' => $newNumber,
                                'invoice_date' => now()->toDateString(),
                                'due_date' => now()->addDays(14)->toDateString(),
                                'type' => $record->type,
                                'subtotal' => $record->subtotal,
                                'discount' => $record->discount,
                                'tax' => $record->tax,
                                'grand_total' => $record->grand_total,
                                'paid_amount' => 0.00,
                                'due_amount' => $record->grand_total,
                                'status' => 'unpaid',
                                'terms' => $record->terms,
                                'is_sent' => false,
                            ]);

                            foreach ($record->items as $item) {
                                $invoice->items()->create([
                                    'product_id' => $item->product_id,
                                    'description' => $item->description,
                                    'quantity' => $item->quantity,
                                    'unit_price' => $item->unit_price,
                                    'tax' => $item->tax ?? 0.00,
                                    'subtotal' => $item->subtotal,
                                ]);
                            }

                            return $invoice;
                        });

                        Notification::make()
                            ->title("Invoice duplicated as #{$newInvoice->invoice_number}")
                            ->success()
                            ->send();

                        return $action->redirect(InvoiceResource::getUrl('view', ['record' => $newInvoice->id]));
                    }),

                EditAction::make()
                    ->visible(fn (Invoice $record): bool => ! ($record->is_sent ?? false)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
