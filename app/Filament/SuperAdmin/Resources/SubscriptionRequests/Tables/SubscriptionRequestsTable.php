<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionRequests\Tables;

use App\Models\AuditLog;
use App\Models\SubscriptionRequest;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('# Ref')
                    ->formatStateUsing(fn ($state) => 'REQ-'.str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label('Company / Tenant')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('plan.name')
                    ->label('Plan Tier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('billing_cycle')
                    ->label('Cycle')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => $state === 'yearly' ? 'primary' : 'gray'),

                TextColumn::make('amount')
                    ->label('Amount (LKR)')
                    ->formatStateUsing(fn ($state) => 'Rs. '.number_format((float) $state, 2))
                    ->sortable(),

                TextColumn::make('payment_reference')
                    ->label('Bank Reference')
                    ->searchable()
                    ->copyable()
                    ->weight('medium'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('approver.name')
                    ->label('Approved By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime('d M Y, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending Verification',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('billing_cycle')
                    ->options([
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ]),
            ])
            ->recordActions([
                Action::make('approve_extend')
                    ->label('Approve & Extend')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Subscription Renewal & Extend Period')
                    ->modalDescription(function (SubscriptionRequest $record): string {
                        $period = $record->billing_cycle === 'yearly' ? '1 Year' : '1 Month';

                        return "Are you sure you want to approve this renewal for {$record->company->name}? This will extend their {$record->plan->name} subscription by {$period} and reactivate the workspace.";
                    })
                    ->visible(fn (SubscriptionRequest $record): bool => $record->status === 'pending')
                    ->action(function (SubscriptionRequest $record) {
                        $company = $record->company;
                        $admin = auth()->user();

                        // Calculate new expiry date: max(now(), current_expiry) + 1 month/year
                        $currentExpiry = $company->subscription_ends_at;
                        $baseDate = ($currentExpiry && $currentExpiry->isFuture())
                            ? $currentExpiry->copy()
                            : now();

                        $newExpiry = $record->billing_cycle === 'yearly'
                            ? $baseDate->addYear()
                            : $baseDate->addMonth();

                        // Update company plan, status, and expiry
                        $company->update([
                            'plan_id' => $record->plan_id,
                            'is_active' => true,
                            'subscription_ends_at' => $newExpiry,
                        ]);

                        // Mark request approved
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => $admin?->id,
                            'approved_at' => now(),
                        ]);

                        // Log approval in AuditLog
                        AuditLog::create([
                            'company_id' => $company->id,
                            'user_id' => $admin?->id,
                            'action' => 'approved_subscription_renewal',
                            'model_type' => SubscriptionRequest::class,
                            'model_id' => $record->id,
                            'ip_address' => request()->ip(),
                            'changes' => [
                                'request_id' => $record->id,
                                'plan' => $record->plan?->name,
                                'billing_cycle' => $record->billing_cycle,
                                'amount' => $record->amount,
                                'payment_reference' => $record->payment_reference,
                                'previous_expiry' => $currentExpiry?->toDateTimeString(),
                                'new_expiry' => $newExpiry->toDateTimeString(),
                                'company_is_active' => true,
                            ],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Subscription Approved & Extended')
                            ->body("Subscription for {$company->name} extended until {$newExpiry->format('d M Y')}.")
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Subscription Request')
                    ->visible(fn (SubscriptionRequest $record): bool => $record->status === 'pending')
                    ->action(function (SubscriptionRequest $record) {
                        $admin = auth()->user();

                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => $admin?->id,
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->warning()
                            ->title('Subscription Request Rejected')
                            ->send();
                    }),
            ]);
    }
}
