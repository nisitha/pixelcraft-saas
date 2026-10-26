<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Trial / Starter',
                'slug' => 'starter',
                'price' => 0.00,
                'currency' => 'LKR',
                'billing_interval' => 'monthly',
                'trial_days' => 14,
                'features' => [
                    '14-Day Free Trial',
                    'Basic Invoicing',
                    '2 Employees',
                    'Standard Quotations',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'price' => 7500.00,
                'currency' => 'LKR',
                'billing_interval' => 'monthly',
                'trial_days' => 14,
                'features' => [
                    'Unlimited Invoices',
                    'Payroll & EPF/ETF',
                    '10 Employees',
                    'Quotation Designer & Inventory',
                    'Priority Support',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 15000.00,
                'currency' => 'LKR',
                'billing_interval' => 'monthly',
                'trial_days' => 14,
                'features' => [
                    'Full Feature Access',
                    'Unlimited Employees',
                    'Gemini AI Assistant',
                    'POS & Serial Tracking',
                    'Dedicated 24/7 Account Manager',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
