<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionPayments\Tables;

use App\Mail\PaymentRejectedMail;
use App\Mail\SubscriptionActivatedMail;
use App\Models\AuditLog;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SubscriptionPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('# Ref')
                    ->formatStateUsing(fn ($state) => 'PAY-'.str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label('Company / Tenant')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('subscriptionPlan.name')
                    ->label('Plan Tier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($record) => ($record->currency ?? 'LKR').' '.number_format((float) $record->amount, 2))
                    ->sortable(),

                TextColumn::make('period_years')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => "{$state} Year".($state > 1 ? 's' : ''))
                    ->badge()
                    ->color('info'),

                TextColumn::make('reference_number')
                    ->label('Bank Ref / Transaction ID')
                    ->searchable()
                    ->copyable()
                    ->weight('medium'),

                TextColumn::make('slip_path')
                    ->label('Bank Slip')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? 'View Slip' : 'No File')
                    ->url(fn ($record) => $record->slip_path ? Storage::url($record->slip_path) : null, shouldOpenInNewTab: true),

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
                    ->label('Reviewed By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approved_at')
                    ->label('Reviewed At')
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
                SelectFilter::make('subscription_plan_id')
                    ->label('Plan Tier')
                    ->options(fn () => SubscriptionPlan::pluck('name', 'id')->toArray()),
            ])
            ->recordActions([
                Action::make('view_slip')
                    ->label('View Slip')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('gray')
                    ->visible(fn (SubscriptionPayment $record): bool => ! empty($record->slip_path))
                    ->url(fn (SubscriptionPayment $record): string => Storage::url($record->slip_path), shouldOpenInNewTab: true),

                Action::make('approve_activate')
                    ->label('Approve & Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (SubscriptionPayment $record): bool => $record->status === 'pending')
                    ->form([
                        TextInput::make('period_years')
                            ->label('Duration in Years')
                            ->numeric()
                            ->default(fn (SubscriptionPayment $record): int => $record->period_years ?: 1)
                            ->required(),
                        DatePicker::make('subscription_ends_at')
                            ->label('Custom Expiry Date (Optional)')
                            ->helperText('Leave empty to automatically calculate based on years.'),
                    ])
                    ->action(function (SubscriptionPayment $record, array $data): void {
                        $years = (int) ($data['period_years'] ?? 1);
                        $company = $record->company;
                        $admin = auth()->user();

                        if (! empty($data['subscription_ends_at'])) {
                            $newExpiry = Carbon::parse($data['subscription_ends_at']);
                        } else {
                            $currentExpiry = $company->subscription_ends_at;
                            $baseDate = ($currentExpiry && $currentExpiry->isFuture())
                                ? $currentExpiry->copy()
                                : now();
                            $newExpiry = $baseDate->addYears($years);
                        }

                        $record->update([
                            'status' => 'approved',
                            'period_years' => $years,
                            'approved_by' => $admin?->id,
                            'approved_at' => now(),
                        ]);

                        $company->update([
                            'subscription_status' => 'active',
                            'subscription_plan_id' => $record->subscription_plan_id ?? $company->subscription_plan_id,
                            'subscription_ends_at' => $newExpiry,
                            'trial_ends_at' => null,
                            'is_active' => true,
                        ]);

                        if (class_exists(AuditLog::class)) {
                            AuditLog::create([
                                'company_id' => $company->id,
                                'user_id' => $admin?->id,
                                'action' => 'approved_subscription_payment',
                                'model_type' => SubscriptionPayment::class,
                                'model_id' => $record->id,
                                'ip_address' => request()->ip(),
                                'changes' => [
                                    'amount' => $record->amount,
                                    'currency' => $record->currency,
                                    'period_years' => $years,
                                    'new_expiry' => $newExpiry->toDateTimeString(),
                                ],
                            ]);
                        }

                        $owner = $company->owner();
                        if ($owner && ! empty($owner->email)) {
                            try {
                                Mail::to($owner->email)->send(new SubscriptionActivatedMail($company, $record));
                            } catch (\Throwable $e) {
                                report($e);
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Payment Approved & Subscription Activated')
                            ->body("Subscription for {$company->name} activated until {$newExpiry->format('d M Y')}.")
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (SubscriptionPayment $record): bool => $record->status === 'pending')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3)
                            ->placeholder('Please state why the payment was rejected (e.g. invalid transaction ID, illegible slip)...'),
                    ])
                    ->action(function (SubscriptionPayment $record, array $data): void {
                        $admin = auth()->user();
                        $reason = $data['rejection_reason'];

                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $reason,
                            'approved_by' => $admin?->id,
                            'approved_at' => now(),
                        ]);

                        $owner = $record->company?->owner();
                        if ($owner && ! empty($owner->email)) {
                            try {
                                Mail::to($owner->email)->send(new PaymentRejectedMail($record->company, $record, $reason));
                            } catch (\Throwable $e) {
                                report($e);
                            }
                        }

                        Notification::make()
                            ->warning()
                            ->title('Subscription Payment Rejected')
                            ->send();
                    }),
            ]);
    }
}
