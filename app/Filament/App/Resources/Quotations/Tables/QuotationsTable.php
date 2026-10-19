<?php

namespace App\Filament\App\Resources\Quotations\Tables;

use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Filament\App\Resources\Quotations\QuotationResource;
use App\Models\Invoice;
use App\Models\Quotation;
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

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Quotation $record): string => QuotationResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('quote_number')
                    ->label('Quote #')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('contact.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('system_kw')
                    ->label('Capacity')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kWp')
                    ->sortable(),
                TextColumn::make('phase_type')
                    ->label('Phase')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state).' Phase')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'three' ? 'warning' : 'gray'),
                TextColumn::make('quote_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('LKR')
                    ->weight('bold')
                    ->sortable(),
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
                    ->url(fn (Quotation $record): string => QuotationResource::getUrl('view', ['record' => $record])),

                Action::make('convertToInvoice')
                    ->label('Convert to Invoice')
                    ->icon(Heroicon::ArrowPathRoundedSquare)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Convert Quotation to Invoice')
                    ->modalDescription('Are you sure you want to convert this accepted quotation into an invoice? All line items, specifications, and pricing will be transferred.')
                    ->modalSubmitActionLabel('Yes, Convert to Invoice')
                    ->visible(fn (Quotation $record): bool => in_array($record->status, ['accepted', 'sent', 'draft']))
                    ->action(function (Quotation $record, Action $action) {
                        $invoice = DB::transaction(function () use ($record) {
                            $invoice = Invoice::create([
                                'company_id' => $record->company_id,
                                'contact_id' => $record->contact_id,
                                'quotation_id' => $record->id,
                                'invoice_number' => 'INV-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                                'invoice_date' => now()->toDateString(),
                                'due_date' => now()->addDays(14)->toDateString(),
                                'type' => 'milestone',
                                'subtotal' => $record->subtotal,
                                'discount' => $record->discount,
                                'tax' => $record->tax,
                                'grand_total' => $record->grand_total,
                                'paid_amount' => 0.00,
                                'due_amount' => $record->grand_total,
                                'status' => 'unpaid',
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

                            $record->update(['status' => 'invoiced']);

                            return $invoice;
                        });

                        Notification::make()
                            ->title('Quotation converted to Invoice #'.$invoice->invoice_number)
                            ->success()
                            ->send();

                        return $action->redirect(InvoiceResource::getUrl('edit', ['record' => $invoice->id]));
                    }),

                Action::make('markAsSent')
                    ->label('Mark as Sent')
                    ->icon(Heroicon::CheckCircle)
                    ->color('info')
                    ->visible(fn (Quotation $record): bool => ! ($record->is_sent ?? false))
                    ->action(function (Quotation $record) {
                        $record->update([
                            'is_sent' => true,
                            'sent_at' => now(),
                            'status' => $record->status === 'draft' ? 'sent' : $record->status,
                        ]);

                        Notification::make()
                            ->title('Quotation marked as Sent')
                            ->success()
                            ->send();
                    }),

                Action::make('unlock')
                    ->label('Unlock & Edit')
                    ->icon(Heroicon::LockOpen)
                    ->color('warning')
                    ->visible(fn (Quotation $record): bool => (bool) ($record->is_sent ?? false))
                    ->action(function (Quotation $record, Action $action) {
                        $record->update(['is_sent' => false]);

                        Notification::make()
                            ->title('Quotation unlocked for editing')
                            ->info()
                            ->send();

                        return $action->redirect(QuotationResource::getUrl('edit', ['record' => $record]));
                    }),

                Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon(Heroicon::ArrowDownTray)
                    ->color('primary')
                    ->action(function (Quotation $record) {
                        $safeNumber = str_replace(['/', '\\'], '-', $record->quote_number);
                        $filename = "Quotation-{$safeNumber}.pdf";

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generateQuotationPdf($record)->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Action::make('deliveryNote')
                    ->label('Delivery Note')
                    ->icon(Heroicon::Truck)
                    ->color('gray')
                    ->action(function (Quotation $record) {
                        $filename = PdfService::getDeliveryNoteFileName($record);

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generateDeliveryNotePdf($record)->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon(Heroicon::DocumentDuplicate)
                    ->color('gray')
                    ->action(function (Quotation $record, Action $action) {
                        $newQuote = DB::transaction(function () use ($record) {
                            $newNumber = 'QUO-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);

                            $quote = Quotation::create([
                                'company_id' => $record->company_id,
                                'contact_id' => $record->contact_id,
                                'quote_number' => $newNumber,
                                'quote_date' => now()->toDateString(),
                                'valid_until' => now()->addDays(30)->toDateString(),
                                'status' => 'draft',
                                'system_kw' => $record->system_kw,
                                'phase_type' => $record->phase_type,
                                'roof_type' => $record->roof_type,
                                'inverter_brand' => $record->inverter_brand,
                                'panel_brand' => $record->panel_brand,
                                'subtotal' => $record->subtotal,
                                'discount' => $record->discount,
                                'tax' => $record->tax,
                                'grand_total' => $record->grand_total,
                                'terms' => $record->terms,
                                'is_sent' => false,
                            ]);

                            foreach ($record->items as $item) {
                                $quote->items()->create([
                                    'product_id' => $item->product_id,
                                    'description' => $item->description,
                                    'quantity' => $item->quantity,
                                    'unit_price' => $item->unit_price,
                                    'tax' => $item->tax ?? 0.00,
                                    'subtotal' => $item->subtotal,
                                ]);
                            }

                            return $quote;
                        });

                        Notification::make()
                            ->title("Quotation duplicated as #{$newQuote->quote_number}")
                            ->success()
                            ->send();

                        return $action->redirect(QuotationResource::getUrl('view', ['record' => $newQuote->id]));
                    }),

                EditAction::make()
                    ->visible(fn (Quotation $record): bool => ! ($record->is_sent ?? false)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
