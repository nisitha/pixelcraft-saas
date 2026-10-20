<?php

namespace App\Filament\App\Resources\TeamMembers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Team Member Credentials & Role')
                    ->description('Assign role permissions and login credentials for this tenant workspace.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Full Name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Email Address')
                                ->email()
                                ->required()
                                ->maxLength(255),

                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable()
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->helperText(fn (string $operation): string => $operation === 'create'
                                    ? 'Set a secure temporary or permanent password for this staff member.'
                                    : 'Leave blank to preserve the existing password.'),

                            Select::make('role')
                                ->label('Tenant Role & Permission Level')
                                ->required()
                                ->options([
                                    'owner' => 'Business Owner (Full Access, Profit & Cost visibility, Billing & Staff control)',
                                    'admin' => 'Operations Manager / Admin (Full operational access, Profit & Reports access)',
                                    'sales_executive' => 'Sales Executive (Contacts, Quotations, Invoices, Sales Agents — No Cost/Profit, No Expenses)',
                                    'cashier' => 'Cashier / Billing Assistant (POS Terminal, Invoices, Payments, Receipts — No Cost/Profit, No Expenses)',
                                    'engineer' => 'Technical / Site Engineer (Technical specs, Certificates, Serial Numbers, Delivery Notes — No Payments/Expenses/POS)',
                                ])
                                ->helperText('Select the permission tier for this staff member in your workspace.')
                                ->default('sales_executive'),

                            Toggle::make('is_active')
                                ->label('Active Staff Account')
                                ->helperText('Disable to immediately revoke workspace access without deleting the user.')
                                ->default(true)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
