<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'br_number' => 'PV-'.fake()->numerify('#####'),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'currency' => 'LKR',
            'address' => fake()->address(),
            'logo_path' => null,
            'is_active' => true,
            'subscription_ends_at' => now()->addDays(30),
            'settings' => [
                'currency' => 'LKR',
                'currency_symbol' => 'Rs.',
            ],
        ];
    }
}
