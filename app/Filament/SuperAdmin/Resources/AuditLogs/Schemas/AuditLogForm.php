<?php

namespace App\Filament\SuperAdmin\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->default(null),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                TextInput::make('action')
                    ->required(),
                TextInput::make('model_type')
                    ->default(null),
                TextInput::make('model_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('ip_address')
                    ->default(null),
                Textarea::make('changes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
