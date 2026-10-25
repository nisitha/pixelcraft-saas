<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Employee;
use App\Models\SalaryAdvance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalaryAdvance>
 */
class SalaryAdvanceFactory extends Factory
{
    protected $model = SalaryAdvance::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'amount' => fake()->randomFloat(2, 5000, 30000),
            'request_date' => fake()->date(),
            'deduct_month' => now()->format('Y-m'),
            'reason' => fake()->sentence(),
            'status' => 'pending',
            'approved_by' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }
}
