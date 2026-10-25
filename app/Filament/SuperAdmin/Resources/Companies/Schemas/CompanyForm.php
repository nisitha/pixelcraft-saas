<?php

namespace App\Filament\SuperAdmin\Resources\Companies\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('br_number')
                    ->label('BR Number')
                    ->default(null),
                Select::make('currency')
                    ->options([
                        'LKR' => 'LKR',
                        'USD' => 'USD',
                        'GBP' => 'GBP',
                        'EUR' => 'EUR',
                        'AED' => 'AED',
                    ])
                    ->default('LKR')
                    ->required(),
                Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->default(null),
                DateTimePicker::make('subscription_ends_at'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                Textarea::make('address')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('logo_path')
                    ->default(null),
                TextInput::make('tax_number')
                    ->default(null),
                Textarea::make('settings')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
