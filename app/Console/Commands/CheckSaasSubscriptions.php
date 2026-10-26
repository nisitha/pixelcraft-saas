<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiredMail;
use App\Mail\TrialReminderMail;
use App\Models\AuditLog;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckSaasSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:check-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SaaS subscriptions and trial status, transition expired accounts, and send milestone reminders';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();
        $expiredCount = 0;
        $reminderCount = 0;

        // 1. Check for expired trials or subscriptions
        $expiredCompanies = Company::query()
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->where('subscription_status', 'trialing')
                        ->whereNotNull('trial_ends_at')
                        ->where('trial_ends_at', '<', $now);
                })->orWhere(function ($q) use ($now) {
                    $q->where('subscription_status', 'active')
                        ->whereNotNull('subscription_ends_at')
                        ->where('subscription_ends_at', '<', $now);
                });
            })
            ->get();

        foreach ($expiredCompanies as $company) {
            $previousStatus = $company->subscription_status;
            $company->update([
                'subscription_status' => 'expired',
            ]);

            if (class_exists(AuditLog::class)) {
                AuditLog::create([
                    'company_id' => $company->id,
                    'action' => 'subscription_expired',
                    'model_type' => Company::class,
                    'model_id' => $company->id,
                    'ip_address' => '127.0.0.1',
                    'changes' => [
                        'previous_status' => $previousStatus,
                        'new_status' => 'expired',
                        'trial_ends_at' => $company->trial_ends_at?->toDateTimeString(),
                        'subscription_ends_at' => $company->subscription_ends_at?->toDateTimeString(),
                    ],
                ]);
            }

            $owner = $company->owner();
            if ($owner && ! empty($owner->email)) {
                try {
                    Mail::to($owner->email)->send(new SubscriptionExpiredMail($company));
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $this->info("Company '{$company->name}' (ID: {$company->id}) expired. Status transitioned from '{$previousStatus}' to 'expired'.");
            $expiredCount++;
        }

        // 2. Check milestone reminders: 7 days, 3 days, 1 day remaining
        $activeOrTrialing = Company::query()
            ->whereIn('subscription_status', ['trialing', 'active'])
            ->get();

        foreach ($activeOrTrialing as $company) {
            $targetDate = $company->subscription_status === 'trialing'
                ? $company->trial_ends_at
                : $company->subscription_ends_at;

            if (! $targetDate || $targetDate->isPast()) {
                continue;
            }

            $daysRemaining = (int) now()->startOfDay()->diffInDays($targetDate->startOfDay(), false);

            if (in_array($daysRemaining, [7, 3, 1], true)) {
                $owner = $company->owner();
                if ($owner && ! empty($owner->email)) {
                    try {
                        Mail::to($owner->email)->send(new TrialReminderMail($company, $daysRemaining));
                        $this->line("Sent {$daysRemaining}-day reminder email to '{$company->name}' ({$owner->email}).");
                        $reminderCount++;
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }
        }

        $this->info("Completed saas:check-subscriptions. Expired: {$expiredCount}, Reminders sent: {$reminderCount}.");

        return Command::SUCCESS;
    }
}
