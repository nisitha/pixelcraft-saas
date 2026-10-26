<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionPayments;

use App\Filament\SuperAdmin\Resources\SubscriptionPayments\Pages\ListSubscriptionPayments;
use App\Filament\SuperAdmin\Resources\SubscriptionPayments\Tables\SubscriptionPaymentsTable;
use App\Models\SubscriptionPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubscriptionPaymentResource extends Resource
{
    protected static ?string $model = SubscriptionPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;

    protected static ?string $navigationLabel = 'Subscription Payments';

    protected static ?string $modelLabel = 'Subscription Payment';

    protected static ?string $pluralModelLabel = 'Subscription Payments';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return SubscriptionPaymentsTable::configure($table);
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
            'index' => ListSubscriptionPayments::route('/'),
        ];
    }
}
