<?php

namespace Database\Factories;

use App\Livewire\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'external_id' => null,
            'date' => today(),
            'description' => fake()->sentence,
            'amount' => to_cents('$'.number_format(fake()->randomFloat(2, 1_000, 50_000), 2)),
            'category' => fake()->randomElement(Expense::categories()),
            'type' => fake()->randomElement(Expense::types()),
            'payment_method' => fake()->randomElement(Expense::paymentMethods()),
            'notes' => null,
        ];
    }
}
