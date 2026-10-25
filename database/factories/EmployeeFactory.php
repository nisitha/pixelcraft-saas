<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->name(),
            'calling_name' => fake()->firstName(),
            'nic_number' => fake()->numerify('199#########'),
            'designation' => fake()->randomElement([
                'Solar Technician',
                'Site Supervisor',
                'Electrician',
                'Office Staff',
                'Sales Engineer',
            ]),
            'contact_number' => fake()->phoneNumber(),
            'daily_rate' => fake()->randomElement([2500.00, 3000.00, 3500.00, null]),
            'basic_salary' => fake()->randomFloat(2, 45000, 150000),
            'fixed_allowance' => fake()->randomFloat(2, 5000, 25000),
            'epf_number' => 'EPF-'.fake()->unique()->numerify('####'),
            'bank_name' => fake()->randomElement(['Commercial Bank', 'Sampath Bank', 'Hatton National Bank', 'Bank of Ceylon']),
            'bank_branch' => fake()->city(),
            'bank_account_number' => fake()->numerify('8000######'),
            'joined_date' => fake()->date(),
            'is_active' => true,
        ];
    }
}
