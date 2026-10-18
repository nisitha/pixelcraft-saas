<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Plan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
                    ->placeholder('e.g. Apex Solar Lanka (Pvt) Ltd'),

                TextInput::make('phone')
                    ->label('Primary Phone Number')
                    ->tel()
                    ->maxLength(30)
                    ->placeholder('+94 77 123 4567'),

                TextInput::make('email')
                    ->label('Official Business Email')
                    ->email()
                    ->maxLength(255)
                    ->placeholder('contact@company.lk'),

                Textarea::make('address')
                    ->label('Registered Business Address')
                    ->rows(2)
                    ->placeholder('No. 45, Galle Road, Colombo 03, Sri Lanka'),

                TextInput::make('tax_number')
                    ->label('Tax / VAT / TIN Number')
                    ->maxLength(50)
                    ->placeholder('e.g. VAT-123456789 or TIN-10293847'),

                Select::make('plan_id')
                    ->label('Select Subscription Plan')
                    ->options(function () {
                        return Plan::where('is_active', true)
                            ->orderBy('price_monthly')
                            ->get()
                            ->mapWithKeys(function (Plan $plan) {
                                return [$plan->id => "{$plan->name} — Rs. ".number_format($plan->price_monthly, 0).' / mo (14-Day Free Trial)'];
                            });
                    })
                    ->default(function () {
                        return Plan::where('slug', 'silver')->value('id')
                            ?? Plan::where('slug', 'bronze')->value('id')
                            ?? Plan::first()?->id;
                    })
                    ->required()
                    ->helperText('Every new company receives a full 14-day free trial on the selected tier. No advance payment required.'),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug ?: 'company-'.Str::lower(Str::random(6));
        $uniqueSlug = $slug;
        $counter = 1;

        while (Company::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug.'-'.(++$counter);
        }

        $defaultPlanId = Plan::where('slug', 'silver')->value('id')
            ?? Plan::where('slug', 'bronze')->value('id')
            ?? Plan::first()?->id;

        $company = Company::create([
            'name' => $data['name'],
            'slug' => $uniqueSlug,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'tax_number' => $data['tax_number'] ?? null,
            'plan_id' => $data['plan_id'] ?? $defaultPlanId,
            'is_active' => true,
            'subscription_ends_at' => now()->addDays(14),
            'settings' => [
                'currency' => 'LKR',
                'currency_symbol' => 'Rs.',
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
                'plan_id' => $company->plan_id,
                'subscription_ends_at' => $company->subscription_ends_at?->toDateTimeString(),
                'trial_days' => 14,
                'role' => 'owner',
            ],
        ]);

        return $company;
    }
}
