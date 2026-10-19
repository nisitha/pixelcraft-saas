<?php

namespace App\Filament\App\Resources\CommissionPayouts\Schemas;

use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CommissionPayoutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Commission Settlement Voucher')
                    ->description('Record official agent commission payouts, generate payment receipts and settle invoice balances')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('voucher_number')
                                ->label('Voucher Number')
                                ->default(fn () => 'COM-VHE-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT))
                                ->required()
                                ->unique(ignoreRecord: true),

                            DatePicker::make('payout_date')
                                ->label('Payout Date')
                                ->default(now())
                                ->required(),

                            Select::make('status')
                                ->label('Voucher Status')
                                ->options([
                                    'draft' => 'Draft',
                                    'paid' => 'Settled / Paid',
                                ])
                                ->default('paid')
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('sales_agent_id')
                                ->label('Sales Agent / Beneficiary')
                                ->relationship('salesAgent', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(),

                            Select::make('invoice_id')
                                ->label('Linked Customer Invoice (Optional)')
                                ->options(function (Get $get) {
                                    $agentId = $get('sales_agent_id');
                                    $query = Invoice::query();
                                    if ($agentId) {
                                        $query->where('sales_agent_id', $agentId);
                                    }

                                    return $query->latest('invoice_date')
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn (Invoice $inv) => [
                                            $inv->id => "#{$inv->invoice_number} - {$inv->contact?->name} (Total: Rs. ".number_format($inv->grand_total, 2).')',
                                        ]);
                                })
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?int $state) {
                                    if ($state && $invoice = Invoice::find($state)) {
                                        if ((float) $invoice->commission_amount > 0) {
                                            $set('amount', (float) $invoice->commission_amount);
                                        }
                                    }
                                }),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('amount')
                                ->label('Settled Amount (Rs.)')
                                ->numeric()
                                ->prefix('Rs.')
                                ->required(),

                            Select::make('payment_method')
                                ->label('Disbursement Method')
                                ->options([
                                    'bank_transfer' => 'Bank Transfer (Remittance)',
                                    'cash' => 'Cash Payout',
                                    'cheque' => 'Cheque',
                                ])
                                ->default('bank_transfer')
                                ->required(),

                            TextInput::make('reference_no')
                                ->label('Bank Reference / Cheque #')
                                ->placeholder('e.g., TXN-991823'),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('paper_size')
                                ->label('PDF Voucher Paper Size')
                                ->options([
                                    'a4' => 'A4 (Standard 210 × 297 mm)',
                                    'a5' => 'A5 (Compact 148 × 210 mm)',
                                ])
                                ->default('a4'),

                            Select::make('orientation')
                                ->label('PDF Layout')
                                ->options([
                                    'portrait' => 'Portrait (Vertical)',
                                    'landscape' => 'Landscape (Horizontal)',
                                ])
                                ->default('portrait'),
                        ]),

                        Textarea::make('notes')
                            ->label('Settlement Remarks / Audit Notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
