<?php

namespace App\Filament\App\Pages;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Plan;
use App\Models\SubscriptionRequest;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class SubscriptionBilling extends Page
{
    use WithFileUploads;

    public const PROVIDER_NAME = 'Pixel Craft Studios';

    public const BANK_NAME = 'Hatton National Bank PLC (HNB)';

    public const BANK_BRANCH = 'Galle Branch';

    public const BANK_ACCOUNT_NAME = 'P M N S T Dharmarathna';

    public const BANK_ACCOUNT_NUMBER = '227020114025';

    public const BANK_COUNTRY = 'Sri Lanka';

    public const PAYPAL_EMAIL = 'pixelcraftstudioslk@gmail.com';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Subscription & Billing';

    protected static ?string $title = 'Subscription & Billing Management';

    protected static ?string $slug = 'subscription-billing';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.app.pages.subscription-billing';

    public ?int $plan_id = null;

    public string $billing_cycle = 'monthly';

    public string $payment_method = 'bank_transfer'; // 'card', 'bank_transfer', 'paypal'

    public string $payment_reference = '';

    public $receipt = null;

    public string $notes = '';

    // Card Simulation fields
    public string $card_name = '';

    public string $card_number = '';

    public string $card_expiry = '';

    public string $card_cvc = '';

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

    public function getEstimatedAmountUsd(): float
    {
        $lkr = $this->getEstimatedAmount();

        return round($lkr / 300, 2);
    }

    /**
     * Format time remaining into clean human-readable text (no decimals).
     *
     * @return array{text: string, badge: string, days: ?int, is_expired: bool}
     */
    public function getHumanReadableTimeRemaining(): array
    {
        $tenant = $this->getTenant();
        $expiry = $tenant?->subscription_ends_at;

        if (! $expiry) {
            return [
                'text' => 'Indefinite / Free Plan',
                'badge' => 'gray',
                'days' => null,
                'is_expired' => false,
            ];
        }

        $now = now();
        $isExpired = $expiry->isPast();
        $days = (int) round($now->diffInDays($expiry, false));

        if ($isExpired) {
            $absDays = abs($days);

            return [
                'text' => 'Expired '.$absDays.' '.Str::plural('day', $absDays).' ago',
                'badge' => 'danger',
                'days' => $days,
                'is_expired' => true,
            ];
        }

        if ($days === 0) {
            return [
                'text' => 'Expires today',
                'badge' => 'warning',
                'days' => 0,
                'is_expired' => false,
            ];
        }

        if ($days >= 30) {
            $months = (int) round($days / 30);

            return [
                'text' => "{$days} Days remaining (~{$months} ".Str::plural('month', $months).')',
                'badge' => 'success',
                'days' => $days,
                'is_expired' => false,
            ];
        }

        return [
            'text' => "{$days} ".Str::plural('day', $days).' remaining',
            'badge' => $days <= 7 ? 'warning' : 'success',
            'days' => $days,
            'is_expired' => false,
        ];
    }

    public function setPaymentMethod(string $method): void
    {
        if (in_array($method, ['card', 'bank_transfer', 'paypal'])) {
            $this->payment_method = $method;
        }
    }

    /**
     * Submit Bank Transfer or PayPal Proof.
     */
    public function submitRenewalRequest(): void
    {
        if ($this->payment_method === 'card') {
            $this->processCardPayment();

            return;
        }

        $this->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'payment_method' => ['required', 'in:card,bank_transfer,paypal'],
            'payment_reference' => ['required', 'string', 'min:3', 'max:100'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_reference.required' => $this->payment_method === 'paypal'
                ? 'Please provide your PayPal Transaction ID.'
                : 'Please provide the bank transfer or deposit slip reference number.',
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

        $receiptPath = null;
        if ($this->receipt) {
            $receiptPath = $this->receipt->store('subscription-receipts', 'public');
        }

        SubscriptionRequest::create([
            'company_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $this->billing_cycle,
            'amount' => $amount,
            'payment_method' => $this->payment_method,
            'payment_reference' => trim($this->payment_reference),
            'receipt_path' => $receiptPath,
            'notes' => trim($this->notes) ?: null,
            'status' => 'pending',
        ]);

        $this->payment_reference = '';
        $this->receipt = null;
        $this->notes = '';

        Notification::make()
            ->success()
            ->title('Payment Proof Submitted')
            ->body('Payment Proof Submitted. Your subscription extension is under review by Pixel Craft Studios.')
            ->persistent()
            ->send();
    }

    /**
     * Direct Online Card Checkout triggering instant subscription renewal.
     */
    public function processCardPayment(): void
    {
        $this->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
        ]);

        $tenant = $this->getTenant();

        if (! $tenant) {
            Notification::make()
                ->danger()
                ->title('Tenant Not Found')
                ->send();

            return;
        }

        $plan = Plan::findOrFail($this->plan_id);
        $amount = $this->getEstimatedAmount();

        $txnReference = 'TXN-CARD-'.strtoupper(Str::random(10));
        $currentExpiry = $tenant->subscription_ends_at;
        $baseDate = ($currentExpiry && $currentExpiry->isFuture()) ? $currentExpiry->copy() : now();
        $newExpiry = $this->billing_cycle === 'yearly' ? $baseDate->addYear() : $baseDate->addMonth();

        // 1. Instantly extend company subscription
        $tenant->update([
            'plan_id' => $plan->id,
            'is_active' => true,
            'subscription_ends_at' => $newExpiry,
        ]);

        // 2. Create approved subscription request
        $req = SubscriptionRequest::create([
            'company_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $this->billing_cycle,
            'amount' => $amount,
            'payment_method' => 'card',
            'payment_reference' => $txnReference,
            'receipt_path' => null,
            'notes' => 'Direct Instant Card Checkout via Pixel Craft Studios Gateway. Auto-approved.',
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // 3. Audit log
        AuditLog::create([
            'company_id' => $tenant->id,
            'user_id' => auth()->id(),
            'action' => 'card_subscription_auto_renewed',
            'model_type' => SubscriptionRequest::class,
            'model_id' => $req->id,
            'ip_address' => request()->ip(),
            'changes' => [
                'plan' => $plan->name,
                'billing_cycle' => $this->billing_cycle,
                'amount' => $amount,
                'payment_reference' => $txnReference,
                'new_expiry' => $newExpiry->toDateTimeString(),
            ],
        ]);

        $this->payment_reference = '';
        $this->receipt = null;
        $this->notes = '';

        Notification::make()
            ->success()
            ->title('Card Payment Successful!')
            ->body("Your {$plan->name} ({$this->billing_cycle}) subscription has been instantly activated until {$newExpiry->format('d M Y')}. Thank you for choosing Pixel Craft Studios.")
            ->persistent()
            ->send();
    }
}
