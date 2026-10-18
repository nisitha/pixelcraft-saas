<?php

namespace App\Filament\App\Pages;

use App\Models\Company;
use App\Models\Plan;
use App\Models\SubscriptionRequest;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class SubscriptionBilling extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Subscription & Billing';

    protected static ?string $title = 'Subscription Status & Renewal';

    protected static ?string $slug = 'subscription-billing';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.app.pages.subscription-billing';

    public ?int $plan_id = null;

    public string $billing_cycle = 'monthly';

    public string $payment_reference = '';

    public string $notes = '';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        return $user->isTenantOwnerOrAdmin();
    }

    public function mount(): void
    {
        $tenant = Filament::getTenant();

        if ($tenant instanceof Company) {
            $this->plan_id = $tenant->plan_id ?? Plan::where('is_active', true)->value('id');
        }
    }

    public function getTenant(): ?Company
    {
        $tenant = Filament::getTenant();

        return $tenant instanceof Company ? $tenant : null;
    }

    /**
     * @return Collection<int, Plan>
     */
    public function getAvailablePlans(): Collection
    {
        return Plan::where('is_active', true)->orderBy('price_monthly')->get();
    }

    /**
     * @return Collection<int, SubscriptionRequest>
     */
    public function getSubscriptionRequests(): Collection
    {
        $tenant = $this->getTenant();

        if (! $tenant) {
            return collect();
        }

        return SubscriptionRequest::where('company_id', $tenant->id)
            ->with(['plan', 'approver'])
            ->latest()
            ->get();
    }

    public function getEstimatedAmount(): float
    {
        $plan = Plan::find($this->plan_id);

        if (! $plan) {
            return 0.00;
        }

        return $this->billing_cycle === 'yearly'
            ? (float) $plan->price_yearly
            : (float) $plan->price_monthly;
    }

    public function submitRenewalRequest(): void
    {
        $this->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'payment_reference' => ['required', 'string', 'min:3', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_reference.required' => 'Please provide the bank transfer or deposit slip reference number.',
        ]);

        $tenant = $this->getTenant();

        if (! $tenant) {
            Notification::make()
                ->danger()
                ->title('Tenant Not Found')
                ->body('Unable to resolve active company tenant.')
                ->send();

            return;
        }

        $plan = Plan::findOrFail($this->plan_id);
        $amount = $this->getEstimatedAmount();

        SubscriptionRequest::create([
            'company_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $this->billing_cycle,
            'amount' => $amount,
            'payment_reference' => trim($this->payment_reference),
            'notes' => trim($this->notes) ?: null,
            'status' => 'pending',
        ]);

        $this->payment_reference = '';
        $this->notes = '';

        Notification::make()
            ->success()
            ->title('Renewal Request Submitted')
            ->body("Your renewal request for {$plan->name} ({$this->billing_cycle}) of Rs. ".number_format($amount, 2).' has been sent to Super Admin for verification.')
            ->persistent()
            ->send();
    }
}
