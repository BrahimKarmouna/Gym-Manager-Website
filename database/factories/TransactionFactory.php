<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Account;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        // Get random account and category IDs (assuming these models exist)
        $sourceAccount = Account::factory()->create();
        $destinationAccount = Account::factory()->create();
        $category = Category::factory()->create();

        return [
            'user_id' => 1, // Assuming user with ID 1 exists
            'date' => $this->faker->date(),
            'amount' => $this->faker->numberBetween(10, 1000),
            'source_account_id' => $sourceAccount?->id,
            'destination_account_id' => $destinationAccount?->id,
            'category_id' => $category?->id,
            'note' => $this->faker->sentence(),
            'transaction_type' => $this->faker->randomElement(TransactionType::cases()),
        ];
    }
}
