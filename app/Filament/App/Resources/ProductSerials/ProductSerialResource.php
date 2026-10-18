<?php

namespace App\Filament\App\Resources\ProductSerials;

use App\Filament\App\Resources\ProductSerials\Pages\CreateProductSerial;
use App\Filament\App\Resources\ProductSerials\Pages\EditProductSerial;
use App\Filament\App\Resources\ProductSerials\Pages\ListProductSerials;
use App\Filament\App\Resources\ProductSerials\Schemas\ProductSerialForm;
use App\Filament\App\Resources\ProductSerials\Tables\ProductSerialsTable;
use App\Models\ProductSerial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductSerialResource extends Resource
{
    protected static ?string $model = ProductSerial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $navigationLabel = 'Serials & Warranties';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return ProductSerialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductSerialsTable::configure($table);
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
            'index' => ListProductSerials::route('/'),
            'create' => CreateProductSerial::route('/create'),
            'edit' => EditProductSerial::route('/{record}/edit'),
        ];
    }
}
