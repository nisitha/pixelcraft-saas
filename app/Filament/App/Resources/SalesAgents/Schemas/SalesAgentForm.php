<?php

namespace App\Filament\App\Resources\SalesAgents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalesAgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Agent / Referrer Profile')
                    ->description('Basic identity and contact information for the introducer or business promoter')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('agent_code')
                                ->label('Agent Code')
                                ->default(fn () => 'AGT-'.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT))
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('name')
                                ->label('Full Name')
                                ->required(),

                            TextInput::make('nic_number')
                                ->label('NIC / Passport No')
                                ->placeholder('e.g., 199182390123 / 918239012V'),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('phone')
                                ->label('Phone / Mobile')
                                ->tel()
                                ->placeholder('077 123 4567'),

                            TextInput::make('email')
                                ->label('Email Address')
                                ->email(),
                        ]),

                        Textarea::make('address')
                            ->label('Residential / Business Address')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Remittance Bank Account Details')
                    ->description('Bank information used for direct commission payout transfers')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('bank_name')
                                ->label('Bank Name')
                                ->placeholder('e.g., Commercial Bank of Ceylon'),

                            TextInput::make('bank_branch')
                                ->label('Branch Name')
                                ->placeholder('e.g., Negombo Branch'),

                            TextInput::make('bank_account_name')
                                ->label('Account Holder Name')
                                ->placeholder('e.g., K. A. Aradhana Fernando'),

                            TextInput::make('bank_account_no')
                                ->label('Account Number')
                                ->placeholder('e.g., 8001928371'),
                        ]),
                    ]),

                Section::make('Commission Policy & Status')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('default_commission_type')
                                ->label('Default Commission Type')
                                ->options([
                                    'fixed' => 'Fixed Amount (LKR)',
                                    'percentage' => 'Percentage of Project (%)',
                                ])
                                ->default('fixed')
                                ->required(),

                            TextInput::make('default_commission_value')
                                ->label('Default Value')
                                ->numeric()
                                ->prefix('Rs. / %')
                                ->default(0.00)
                                ->required(),

                            Toggle::make('is_active')
                                ->label('Active Agent')
                                ->default(true)
                                ->inline(false),
                        ]),

                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
