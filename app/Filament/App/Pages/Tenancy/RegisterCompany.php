<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Plan;
use App\Models\SubscriptionPlan;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema as DbSchema;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Your Company';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Company / Business Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. PixelCraft Solutions (Pvt) Ltd'),

                TextInput::make('br_number')
                    ->label('Business Registration (BR) Number')
                    ->maxLength(100)
                    ->placeholder('e.g. PV-123456 or BR-987654'),

                TextInput::make('phone')
                    ->label('Primary Phone Number')
                    ->tel()
                    ->required()
                    ->maxLength(30)
                    ->placeholder('+94 77 123 4567'),

                Select::make('currency')
                    ->label('Currency')
                    ->options([
                        'LKR' => 'LKR - Sri Lankan Rupee',
                        'USD' => 'USD - US Dollar',
                        'GBP' => 'GBP - British Pound',
                        'EUR' => 'EUR - Euro',
                        'AED' => 'AED - UAE Dirham',
                    ])
                    ->default('LKR')
                    ->required(),

                Textarea::make('address')
                    ->label('Business Address')
                    ->rows(2)
                    ->placeholder('No. 45, Galle Road, Colombo 03, Sri Lanka'),

                FileUpload::make('logo_path')
                    ->label('Company Logo')
                    ->disk('company-logos')
                    ->directory('logos')
                    ->image()
                    ->visibility('public')
                    ->maxSize(2048),

                TextInput::make('email')
                    ->label('Official Business Email')
                    ->email()
                    ->maxLength(255)
                    ->placeholder('contact@company.lk'),

                TextInput::make('tax_number')
                    ->label('Tax / VAT / TIN Number')
                    ->maxLength(50)
                    ->placeholder('e.g. VAT-123456789 or TIN-10293847'),

                Radio::make('subscription_plan_id')
                    ->label('Select Subscription Plan')
                    ->options(function () {
                        if (DbSchema::hasTable('subscription_plans')) {
                            $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
                            if ($plans->isNotEmpty()) {
                                return $plans->mapWithKeys(function (SubscriptionPlan $plan) {
                                    $priceDisplay = $plan->price <= 0
                                        ? 'Free Trial — LKR 0 (14-Day Full Access)'
                                        : "{$plan->currency} ".number_format($plan->price, 0).' / '.ucfirst($plan->billing_interval);

                                    return [$plan->id => "{$plan->name} — {$priceDisplay}"];
                                });
                            }
                        }

                        return [
                            1 => 'Free Trial / Starter — Free Trial — LKR 0 (14-Day Full Access)',
                            2 => 'Professional — LKR 7,500 / Monthly',
                            3 => 'Enterprise — LKR 15,000 / Monthly',
                        ];
                    })
                    ->descriptions(function () {
                        if (DbSchema::hasTable('subscription_plans')) {
                            $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
                            if ($plans->isNotEmpty()) {
                                return $plans->mapWithKeys(function (SubscriptionPlan $plan) {
                                    $features = is_array($plan->features)
                                        ? implode(' • ', $plan->features)
                                        : (string) $plan->features;

                                    return [$plan->id => $features ?: '14-Day Free Trial period included.'];
                                });
                            }
                        }

                        return [
                            1 => '14-Day Free Trial • Basic Invoicing • 2 Employees',
                            2 => 'Unlimited Invoices • Payroll & EPF/ETF • 10 Employees',
                            3 => 'Full Feature Access • Unlimited Employees • Gemini AI Assistant',
                        ];
                    })
                    ->default(function () {
                        if (DbSchema::hasTable('subscription_plans')) {
                            return SubscriptionPlan::where('slug', 'starter')->value('id')
                                ?? SubscriptionPlan::where('is_active', true)->orderBy('price')->value('id');
                        }

                        return 1;
                    })
                    ->required()
                    ->helperText('Every new company receives a full 14-day free trial on the selected tier. No advance payment required.'),

                Hidden::make('plan_id')
                    ->dehydrated(),
            ]);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        if (empty($data['subscription_plan_id']) && ! empty($data['plan_id'])) {
            $data['subscription_plan_id'] = SubscriptionPlan::where('id', $data['plan_id'])->value('id')
                ?? SubscriptionPlan::where('slug', 'starter')->value('id')
                ?? SubscriptionPlan::first()?->id;
        }

        return $data;
    }

    protected function handleRegistration(array $data): Model
    {
        $currency = $data['currency'] ?? 'LKR';
        $currencySymbol = match ($currency) {
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            'AED' => 'AED',
            default => 'Rs.',
        };

        // Extract selected subscription plan
        $subscriptionPlanId = $data['subscription_plan_id'] ?? null;
        if (! $subscriptionPlanId && ! empty($data['plan_id'])) {
            $subscriptionPlanId = SubscriptionPlan::where('id', $data['plan_id'])->value('id')
                ?? SubscriptionPlan::where('slug', 'starter')->value('id')
                ?? SubscriptionPlan::first()?->id;
        }

        if (! $subscriptionPlanId && DbSchema::hasTable('subscription_plans')) {
            $subscriptionPlanId = SubscriptionPlan::where('slug', 'starter')->value('id')
                ?? SubscriptionPlan::where('is_active', true)->orderBy('price')->value('id')
                ?? SubscriptionPlan::first()?->id;
        }

        $subscriptionPlan = $subscriptionPlanId ? SubscriptionPlan::find($subscriptionPlanId) : null;
        $trialDays = $subscriptionPlan?->trial_days ?? 14;
        $trialEndsAt = now()->addDays($trialDays);

        // Map legacy plan_id for backward compatibility
        $legacyPlanId = $data['plan_id'] ?? null;
        if (! $legacyPlanId && $subscriptionPlan) {
            $legacyPlanId = Plan::where('slug', $subscriptionPlan->slug)->value('id')
                ?? Plan::where('slug', 'silver')->value('id')
                ?? Plan::where('slug', 'bronze')->value('id')
                ?? Plan::first()?->id;
        }

        $company = Company::create([
            'name' => $data['name'],
            'br_number' => $data['br_number'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'currency' => $currency,
            'address' => $data['address'] ?? null,
            'logo_path' => $data['logo_path'] ?? null,
            'tax_number' => $data['tax_number'] ?? null,
            'subscription_plan_id' => $subscriptionPlanId,
            'trial_ends_at' => $trialEndsAt,
            'plan_id' => $legacyPlanId,
            'subscription_ends_at' => $trialEndsAt,
            'is_active' => true,
            'settings' => [
                'currency' => $currency,
                'currency_symbol' => $currencySymbol,
                'default_tax_rate' => 0.00,
            ],
        ]);

        $user = auth()->user();
        if ($user) {
            $company->users()->syncWithoutDetaching([
                $user->id => ['role' => 'owner'],
            ]);
        }

        AuditLog::create([
            'company_id' => $company->id,
            'user_id' => $user?->id,
            'action' => 'registered_tenant',
            'model_type' => Company::class,
            'model_id' => $company->id,
            'ip_address' => request()->ip(),
            'changes' => [
                'name' => $company->name,
                'slug' => $company->slug,
                'br_number' => $company->br_number,
                'currency' => $company->currency,
                'role' => 'owner',
            ],
        ]);

        return $company;
    }

    protected function getRedirectUrl(): ?string
    {
        return Filament::getUrl($this->tenant);
    }
}
