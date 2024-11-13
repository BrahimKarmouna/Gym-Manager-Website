<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $incomes = include database_path("data/incomes.php");
    $expenses = include database_path("data/expenses.php");

    foreach ($incomes as $income) {
      Category::create([
        "name" => $income["name"],
        "emoji" => $income["emoji"],
        "transaction_type" => $income["transaction_type"]
      ]);
    }

    foreach ($expenses as $expense) {
      Category::create([
        "name" => $expense["name"],
        "emoji" => $expense["emoji"],
        "transaction_type" => $expense["transaction_type"]
      ]);
    }
  }
}
