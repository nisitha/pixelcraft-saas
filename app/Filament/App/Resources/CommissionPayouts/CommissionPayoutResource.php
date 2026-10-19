<?php

namespace App\Filament\App\Resources\CommissionPayouts;

use App\Filament\App\Resources\CommissionPayouts\Pages\CreateCommissionPayout;
use App\Filament\App\Resources\CommissionPayouts\Pages\EditCommissionPayout;
use App\Filament\App\Resources\CommissionPayouts\Pages\ListCommissionPayouts;
use App\Filament\App\Resources\CommissionPayouts\Schemas\CommissionPayoutForm;
use App\Filament\App\Resources\CommissionPayouts\Tables\CommissionPayoutsTable;
use App\Models\CommissionPayout;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommissionPayoutResource extends Resource
{
    protected static ?string $model = CommissionPayout::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Commission Payouts';

    protected static ?string $modelLabel = 'Commission Payout Voucher';

    protected static ?string $pluralModelLabel = 'Commission Payout Vouchers';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return CommissionPayoutForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommissionPayoutsTable::configure($table);
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
            'index' => ListCommissionPayouts::route('/'),
            'create' => CreateCommissionPayout::route('/create'),
            'edit' => EditCommissionPayout::route('/{record}/edit'),
        ];
    }
}
