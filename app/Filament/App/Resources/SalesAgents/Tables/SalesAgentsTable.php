<?php

namespace App\Filament\App\Resources\SalesAgents\Tables;

use App\Filament\App\Resources\SalesAgents\SalesAgentResource;
use App\Models\CommissionPayout;
use App\Models\Invoice;
use App\Models\SalesAgent;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesAgentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (SalesAgent $record): string => SalesAgentResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('agent_code')
                    ->label('Code')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Agent Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Contact')
                    ->searchable(),

                TextColumn::make('bank_account_no')
                    ->label('Bank Remittance')
                    ->formatStateUsing(fn ($record) => $record->bank_name ? "{$record->bank_name} - {$record->bank_account_no}" : '—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('default_commission_info')
                    ->label('Commission Rate')
                    ->state(function (SalesAgent $record): string {
                        if ($record->default_commission_type === 'percentage') {
                            return "{$record->default_commission_value}% of Project";
                        }

                        return 'Rs. '.number_format((float) $record->default_commission_value, 2);
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_projects_referred')
                    ->label('Projects')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('total_commission_earned')
                    ->label('Earned (LKR)')
                    ->money('LKR')
                    ->weight('bold')
                    ->sortable(query: fn ($query, $direction) => $query->withSum(['invoices' => fn ($q) => $q->where('status', '!=', 'cancelled')], 'commission_amount')->orderBy('invoices_sum_commission_amount', $direction)),

                TextColumn::make('total_commission_paid')
                    ->label('Paid (LKR)')
                    ->money('LKR')
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('unpaid_balance')
                    ->label('Unpaid Balance')
                    ->money('LKR')
                    ->badge()
                    ->color(fn (float $state): string => $state > 0 ? 'danger' : 'success')
                    ->weight('bold'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('payCommission')
                    ->label('Pay Commission')
                    ->icon(Heroicon::Banknotes)
                    ->color('success')
                    ->schema(function (SalesAgent $record): array {
                        $unpaidInvoices = $record->invoices()
                            ->where('commission_status', '!=', 'paid')
                            ->where('commission_amount', '>', 0)
                            ->get()
                            ->mapWithKeys(fn (Invoice $inv) => [
                                $inv->id => "#{$inv->invoice_number} - Customer: {$inv->contact?->name} (Commission: Rs. ".number_format($inv->commission_amount, 2).')',
                            ]);

                        return [
                            Select::make('invoice_id')
                                ->label('Linked Project Invoice (Optional)')
                                ->options($unpaidInvoices)
                                ->placeholder('Select project or leave empty for lump-sum payout')
                                ->live()
                                ->afterStateUpdated(function (Select $component, $state, $set) use ($record) {
                                    if ($state && $inv = Invoice::find($state)) {
                                        $set('amount', (float) $inv->commission_amount);
                                    } else {
                                        $set('amount', max(0, $record->unpaid_balance));
                                    }
                                }),

                            DatePicker::make('payout_date')
                                ->label('Payout Date')
                                ->default(now())
                                ->required(),

                            TextInput::make('amount')
                                ->label('Payout Amount (Rs.)')
                                ->numeric()
                                ->prefix('Rs.')
                                ->default(fn () => max(0, $record->unpaid_balance))
                                ->required(),

                            Select::make('payment_method')
                                ->label('Payment Method')
                                ->options([
                                    'bank_transfer' => 'Bank Transfer (Remittance)',
                                    'cash' => 'Cash',
                                    'cheque' => 'Cheque',
                                ])
                                ->default('bank_transfer')
                                ->required(),

                            TextInput::make('reference_no')
                                ->label('Bank Ref / Cheque No')
                                ->placeholder('e.g., TXN-819284 / CHQ-1002'),

                            Textarea::make('notes')
                                ->label('Voucher Notes')
                                ->placeholder('e.g., Commission settlement for July solar installations.'),
                        ];
                    })
                    ->action(function (SalesAgent $record, array $data) {
                        $voucherNo = 'COM-VHE-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);

                        $payout = CommissionPayout::create([
                            'company_id' => $record->company_id,
                            'sales_agent_id' => $record->id,
                            'invoice_id' => $data['invoice_id'] ?? null,
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

                        if (! empty($data['invoice_id']) && $invoice = Invoice::find($data['invoice_id'])) {
                            $invoice->update(['commission_status' => 'paid']);
                        }

                        Notification::make()
                            ->title("Commission Payout {$payout->voucher_number} processed successfully")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
