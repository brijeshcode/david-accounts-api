<?php

namespace Database\Factories;

use App\Models\ExpenseType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseArticle>
 */
class ExpenseArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     public function definition()
    {
        return [
            'expense_type_id' => ExpenseType::factory(),
            'name' => $this->faker->words(2, true),
            'unit' => $this->faker->randomElement(config('app.system_units')),
            'note' => $this->faker->optional(0.3)->sentence(),
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
        ];
    }

    /**
     * Indicate that the expense article is active.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the expense article is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Create expense article with specific unit.
     */
    public function withUnit($unit)
    {
        return $this->state(function (array $attributes) use ($unit) {
            return [
                'unit' => $unit,
            ];
        });
    }

    /**
     * Create expense article with note.
     */
    public function withNote()
    {
        return $this->state(function (array $attributes) {
            return [
                'note' => $this->faker->paragraph(),
            ];
        });
    }
}