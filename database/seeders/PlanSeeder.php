<?php
namespace Database\Seeders;

// use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
  public function run()
  {
    Plan::create(['name' => 'Monthly Plan', 'price' => 50, 'duration' => '1 ']);
    Plan::create(['name' => 'Yearly Plan', 'price' => 500, 'duration' => '1 ']);
  }
}
