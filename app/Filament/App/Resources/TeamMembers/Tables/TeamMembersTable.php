<?php

namespace App\Filament\App\Resources\TeamMembers\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Staff Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->copyable()
                    ->icon(Heroicon::Envelope),

                TextColumn::make('pivot.role')
                    ->label('Tenant Role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'owner' => 'success',
                        'admin' => 'warning',
                        'sales_executive' => 'info',
                        'cashier' => 'gray',
                        'engineer' => 'primary',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'owner' => 'Business Owner',
                        'admin' => 'Operations Manager',
                        'sales_executive' => 'Sales Executive',
                        'cashier' => 'Cashier / Billing',
                        'engineer' => 'Site Engineer',
                        default => ucfirst($state ?? 'Staff'),
                    }),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon(Heroicon::CheckCircle)
                    ->falseIcon(Heroicon::XCircle)
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('pivot.created_at')
                    ->label('Joined Date')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),

                Action::make('remove')
                    ->label('Remove from Team')
                    ->icon(Heroicon::Trash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Remove Staff Member')
                    ->modalDescription(fn (User $record): string => "Are you sure you want to remove {$record->name} from this company? They will lose access to all company records immediately.")
                    ->action(function (User $record) {
                        $tenant = Filament::getTenant();

                        // Guard against removing self if sole owner
                        $currentUser = Filament::auth()->user();
                        if ($record->id === $currentUser->id) {
                            $ownerCount = $tenant->users()->wherePivot('role', 'owner')->count();
                            if ($ownerCount <= 1) {
                                Notification::make()
                                    ->title('Cannot Remove Sole Owner')
                                    ->body('You are the only owner of this company. Assign another owner before leaving.')
                                    ->danger()
                                    ->send();

                                return;
                            }
                        }

                        $tenant->users()->detach($record->id);

                        Notification::make()
                            ->title("{$record->name} removed from team")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
