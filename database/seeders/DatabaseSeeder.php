<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    $this->call([
      GymSeeder::class,

      ClientsSeeder::class,
      PlanSeeder::class,
      PaymentSeeder::class,
    ]);

  }
}
