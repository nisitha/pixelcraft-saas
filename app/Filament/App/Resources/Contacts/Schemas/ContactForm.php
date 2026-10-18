<?php

namespace App\Filament\App\Resources\Contacts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Details')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('type')
                                ->options([
                                    'customer' => 'Customer',
                                    'supplier' => 'Supplier',
                                ])
                                ->required()
                                ->default('customer'),
                            TextInput::make('name')
                                ->required(),
                            TextInput::make('phone')
                                ->tel(),
                            TextInput::make('email')
                                ->email(),
                            TextInput::make('ceb_account_no')
                                ->label('CEB Account Number (Solar Grid)'),
                            TextInput::make('tax_no')
                                ->label('Tax / NIC Number'),
                            TextInput::make('balance')
                                ->numeric()
                                ->default(0.00)
                                ->prefix('Rs.'),
                            Textarea::make('address')
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
