<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Company;
use Illuminate\Console\Command;

class CheckTrialExpiryAndSendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-trials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tenant trial expiry dates, transition expired trials, and log daily expiry reminders';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        // 1. Transition expired trials
        $expiredCompanies = Company::where('subscription_status', 'trialing')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', $now)
            ->get();

        foreach ($expiredCompanies as $company) {
            $company->update([
                'subscription_status' => 'expired',
            ]);

            AuditLog::create([
                'company_id' => $company->id,
                'action' => 'trial_expired',
                'model_type' => Company::class,
                'model_id' => $company->id,
                'ip_address' => '127.0.0.1',
                'changes' => [
                    'previous_status' => 'trialing',
                    'new_status' => 'expired',
                    'trial_ends_at' => $company->trial_ends_at?->toDateTimeString(),
                    'reason' => '14-day free trial elapsed without plan activation',
                ],
            ]);

            $this->info("Company ID {$company->id} ({$company->name}) trial expired. Status set to 'expired'.");
        }

        // 2. Daily reminder for trials expiring within 3 days
        $expiringSoon = Company::where('subscription_status', 'trialing')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>=', $now)
            ->where('trial_ends_at', '<=', $now->copy()->addDays(3))
            ->get();

        foreach ($expiringSoon as $company) {
            $daysLeft = max(0, round($now->diffInDays($company->trial_ends_at, false)));

            AuditLog::create([
                'company_id' => $company->id,
                'action' => 'trial_expiry_reminder',
                'model_type' => Company::class,
                'model_id' => $company->id,
                'ip_address' => '127.0.0.1',
                'changes' => [
                    'days_remaining' => $daysLeft,
                    'trial_ends_at' => $company->trial_ends_at?->toDateTimeString(),
                    'message' => "Trial expires in {$daysLeft} day(s). Please choose a subscription plan.",
                ],
            ]);

            $this->line("Company ID {$company->id} ({$company->name}) trial expiring soon ({$daysLeft} days remaining).");
        }

        $this->info("Completed trial check: {$expiredCompanies->count()} expired, {$expiringSoon->count()} reminders recorded.");

        return Command::SUCCESS;
    }
}
