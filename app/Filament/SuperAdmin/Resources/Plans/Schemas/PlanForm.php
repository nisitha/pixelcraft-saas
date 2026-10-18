<?php

namespace App\Filament\SuperAdmin\Resources\Plans\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('price_monthly')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('price_yearly')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('max_users')
                    ->required()
                    ->numeric()
                    ->default(5),
                TextInput::make('max_invoices')
                    ->required()
                    ->numeric()
                    ->default(100),
                Textarea::make('features')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
