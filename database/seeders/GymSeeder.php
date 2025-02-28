<?php

namespace Database\Seeders;

use App\Models\Gym;
use Illuminate\Database\Seeder;

class GymSeeder extends Seeder
{
  public function run()
  {
    Gym::create(['name' => 'Elite Gym', 'user_id' => '1', 'location' => 'Downtown']);
    Gym::create(['name' => 'Fitness Pro', 'user_id' => '1', 'location' => 'City Center']);
  }
}

