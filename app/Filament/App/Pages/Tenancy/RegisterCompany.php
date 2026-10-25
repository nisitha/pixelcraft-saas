<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Plan;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

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

                Select::make('plan_id')
                    ->label('Subscription Plan')
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
                    ->helperText('Every new company receives a full 14-day free trial on the selected tier. No advance payment required.'),
            ]);
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

        $defaultPlanId = Plan::where('slug', 'silver')->value('id')
            ?? Plan::where('slug', 'bronze')->value('id')
            ?? Plan::first()?->id;

        $company = Company::create([
            'name' => $data['name'],
            'br_number' => $data['br_number'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'currency' => $currency,
            'address' => $data['address'] ?? null,
            'logo_path' => $data['logo_path'] ?? null,
            'tax_number' => $data['tax_number'] ?? null,
            'plan_id' => $data['plan_id'] ?? $defaultPlanId,
            'is_active' => true,
            'subscription_ends_at' => now()->addDays(14),
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
}
