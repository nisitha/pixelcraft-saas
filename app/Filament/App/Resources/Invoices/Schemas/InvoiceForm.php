<?php

namespace App\Filament\App\Resources\Invoices\Schemas;

use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.app.components.zoho-document-editor')
                    ->columnSpanFull(),
            ]);
    }
}
