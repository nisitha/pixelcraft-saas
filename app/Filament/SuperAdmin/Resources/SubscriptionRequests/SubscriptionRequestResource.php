<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionRequests;

use App\Filament\SuperAdmin\Resources\SubscriptionRequests\Pages\ListSubscriptionRequests;
use App\Filament\SuperAdmin\Resources\SubscriptionRequests\Tables\SubscriptionRequestsTable;
use App\Models\SubscriptionRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubscriptionRequestResource extends Resource
{
    protected static ?string $model = SubscriptionRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CreditCard;

    protected static ?string $navigationLabel = 'Subscription Requests';

    protected static ?string $modelLabel = 'Subscription Request';

    protected static ?string $pluralModelLabel = 'Subscription Requests';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return SubscriptionRequestsTable::configure($table);
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
            'index' => ListSubscriptionRequests::route('/'),
        ];
    }
}
