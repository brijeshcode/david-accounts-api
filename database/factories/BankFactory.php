<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bank>
 */
class BankFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'starting_balance' => $this->faker->randomFloat(2, 1000, 100000),
            'balance' => $this->faker->randomFloat(2, 1000, 100000),
            'address' => $this->faker->address,
            'account_no' => $this->faker->bankAccountNumber,
            'note' => $this->faker->sentence,
            'is_active' => $this->faker->boolean(80), // 80% chance to be true
            'created_by_id' => 1, // Assuming admin user ID is 1
            'created_by_ip' => $this->faker->ipv4,
            'created_by_agent' => $this->faker->userAgent,
        ];
    }
}
