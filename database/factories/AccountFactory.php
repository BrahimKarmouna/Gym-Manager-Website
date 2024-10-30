<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
      $user = User::factory()->create();
        return [
          'name' => fake()->name(),
          'rib' => fake()->iban(),
          'balance' => fake()->numberBetween(1000, 1000000),
          'user_id' => $user->id,
          'account_type' => fake()->randomElement(AccountType::cases()),
          'total_expense' => fake()->numberBetween(1000, 1000000),
          'total_income' => fake()->numberBetween(1000, 1000000),
        ];
    }
}
