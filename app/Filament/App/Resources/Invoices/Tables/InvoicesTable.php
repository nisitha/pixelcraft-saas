<?php

namespace App\Filament\App\Resources\Invoices\Tables;

use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Models\CommissionPayout;
use App\Models\CompletionCertificate;
use App\Models\Invoice;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                TextColumn::make('total_cost')
                    ->label('Total Cost (LKR)')
                    ->money('LKR')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
                TextColumn::make('net_profit')
                    ->label('Net Profit (LKR)')
                    ->money('LKR')
                    ->badge()
                    ->color(fn (float $state): string => $state >= 0 ? 'success' : 'danger')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
                TextColumn::make('profit_margin_percentage')
                    ->label('Profit Margin (%)')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 1).'%')
                    ->badge()
                    ->color(fn (float $state): string => $state >= 20 ? 'success' : ($state >= 0 ? 'warning' : 'danger'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
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

                Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon(Heroicon::ArrowDownTray)
                    ->color('primary')
                    ->action(function (Invoice $record) {
                        $safeNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
                        $filename = "Invoice-{$safeNumber}.pdf";

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generateInvoicePdf($record, 'itemized')->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Action::make('downloadCustomerPdf')
                    ->label('Customer Package PDF')
                    ->icon(Heroicon::DocumentText)
                    ->color('gray')
                    ->action(function (Invoice $record) {
                        $safeNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
                        $filename = "Invoice-{$safeNumber}-Package.pdf";

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generateInvoicePdf($record, 'package_total_only')->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Action::make('issueCertificate')
                    ->label('Issue Certificate')
                    ->icon(Heroicon::AcademicCap)
                    ->color('info')
                    ->action(function (Invoice $record, Action $action) {
                        $certificate = CompletionCertificate::create([
                            'company_id' => $record->company_id,
                            'contact_id' => $record->contact_id,
                            'invoice_id' => $record->id,
                            'quotation_id' => $record->quotation_id,
                            'certificate_number' => 'CERT-VHE/'.date('Y').'/RU/'.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT).'/'.str_pad((string) rand(1, 99), 3, '0', STR_PAD_LEFT),
                            'customer_name' => strtoupper($record->contact?->name ?? 'Customer'),
                            'system_capacity' => ($record->quotation?->system_kw ? $record->quotation->system_kw.' kW Solar System' : '05 kW+05kw Battery offgrid Solar System'),
                            'site_address' => $record->contact?->address ?: '260/20, Negombo Road, Sri Lanka',
                            'commissioning_date' => now()->toDateString(),
                            'grid_connection' => 'On-Grid Net Metering',
                            'warranty_period' => '30 Years (Panels) / 10 Years (Inverter+Battery)',
                            'system_status' => 'Commissioned & Operational',
                            'verification_status' => 'Engineering Safety Compliant',
                            'compliance_note' => 'Installed, tested, and commissioned in accordance with standard safety regulations.',
                            'lead_engineer_name' => 'Lead Engineer',
                            'lead_engineer_title' => 'Technical Operations',
                            'director_name' => 'Managing Director',
                            'director_title' => $record->company?->name ?: 'Vertex Home Solutions (Pvt) Ltd',
                            'signed_date' => now()->toDateString(),
                            'paper_size' => 'a4',
                            'orientation' => 'landscape',
                            'status' => 'draft',
                        ]);

                        Notification::make()
                            ->title("Completion Certificate created: {$certificate->certificate_number}")
                            ->success()
                            ->send();

                        return $action->redirect(CompletionCertificateResource::getUrl('edit', ['record' => $certificate->id]));
                    }),

                Action::make('payCommission')
                    ->label('Pay Commission')
                    ->icon(Heroicon::Banknotes)
                    ->color('success')
                    ->visible(fn (Invoice $record): bool => (bool) $record->sales_agent_id && $record->commission_status !== 'paid')
                    ->schema([
                        DatePicker::make('payout_date')
                            ->label('Payout Date')
                            ->default(now())
                            ->required(),
                        TextInput::make('amount')
                            ->label('Commission Amount (Rs.)')
                            ->numeric()
                            ->prefix('Rs.')
                            ->default(fn (Invoice $record) => (float) $record->commission_amount)
                            ->required(),
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'bank_transfer' => 'Bank Transfer',
                                'cash' => 'Cash',
                                'cheque' => 'Cheque',
                            ])
                            ->default('bank_transfer')
                            ->required(),
                        TextInput::make('reference_no')
                            ->label('Bank Ref / Cheque No')
                            ->placeholder('e.g., TXN-982183'),
                        Textarea::make('notes')
                            ->label('Settlement Notes'),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        $voucherNo = 'COM-VHE-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);

                        $payout = CommissionPayout::create([
                            'company_id' => $record->company_id,
                            'sales_agent_id' => $record->sales_agent_id,
                            'invoice_id' => $record->id,
                            'voucher_number' => $voucherNo,
                            'payout_date' => $data['payout_date'],
                            'amount' => (float) $data['amount'],
                            'payment_method' => $data['payment_method'],
                            'reference_no' => $data['reference_no'] ?? null,
                            'status' => 'paid',
                            'notes' => $data['notes'] ?? null,
                            'paper_size' => 'a4',
                            'orientation' => 'portrait',
                        ]);

                        $record->update(['commission_status' => 'paid']);

                        Notification::make()
                            ->title("Commission Voucher {$payout->voucher_number} created & marked as paid")
                            ->success()
                            ->send();
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
                                'sales_agent_id' => $record->sales_agent_id,
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
                                'commission_amount' => $record->commission_amount ?? 0.00,
                                'commission_status' => 'pending',
                                'status' => 'unpaid',
                                'terms' => $record->terms,
                                'is_sent' => false,
                            ]);

                            foreach ($record->items as $item) {
                                $invoice->items()->create([
                                    'product_id' => $item->product_id,
                                    'description' => $item->description,
                                    'quantity' => $item->quantity,
                                    'cost_price' => $item->cost_price ?? 0.00,
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
