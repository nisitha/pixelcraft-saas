<?php

namespace App\Filament\App\Resources\Quotations;

use App\Filament\App\Resources\Quotations\Pages\CreateQuotation;
use App\Filament\App\Resources\Quotations\Pages\EditQuotation;
use App\Filament\App\Resources\Quotations\Pages\ListQuotations;
use App\Filament\App\Resources\Quotations\Pages\ViewQuotation;
use App\Filament\App\Resources\Quotations\Schemas\QuotationForm;
use App\Filament\App\Resources\Quotations\Tables\QuotationsTable;
use App\Models\Quotation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function form(Schema $schema): Schema
    {
        return QuotationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotations::route('/'),
            'create' => CreateQuotation::route('/create'),
            'view' => ViewQuotation::route('/{record}'),
            'edit' => EditQuotation::route('/{record}/edit'),
        ];
    }
}
