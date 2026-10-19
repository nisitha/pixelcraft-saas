<?php

namespace App\Filament\App\Resources\CompletionCertificates;

use App\Filament\App\Resources\CompletionCertificates\Pages\CreateCompletionCertificate;
use App\Filament\App\Resources\CompletionCertificates\Pages\EditCompletionCertificate;
use App\Filament\App\Resources\CompletionCertificates\Pages\ListCompletionCertificates;
use App\Filament\App\Resources\CompletionCertificates\Pages\ViewCompletionCertificate;
use App\Filament\App\Resources\CompletionCertificates\Schemas\CompletionCertificateForm;
use App\Filament\App\Resources\CompletionCertificates\Tables\CompletionCertificatesTable;
use App\Models\CompletionCertificate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompletionCertificateResource extends Resource
{
    protected static ?string $model = CompletionCertificate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $navigationLabel = 'Certificates';

    protected static ?string $modelLabel = 'Completion Certificate';

    protected static ?string $pluralModelLabel = 'Completion Certificates';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return CompletionCertificateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompletionCertificatesTable::configure($table);
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
            'index' => ListCompletionCertificates::route('/'),
            'create' => CreateCompletionCertificate::route('/create'),
            'view' => ViewCompletionCertificate::route('/{record}'),
            'edit' => EditCompletionCertificate::route('/{record}/edit'),
        ];
    }
}
