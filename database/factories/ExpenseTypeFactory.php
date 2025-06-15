<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseType>
 */
class ExpenseTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'parent_id' => null, // Will be set when creating hierarchical structures
            'note' => $this->faker->optional(0.3)->sentence(10),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the expense type should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the expense type should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an expense type with a specific parent.
     */
    public function withParent(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Create an expense type with a note.
     */
    public function withNote(): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => $this->faker->paragraph(3),
        ]);
    }

    /**
     * Create an expense type without a note.
     */
    public function withoutNote(): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => null,
        ]);
    }
}
