<?php

namespace Database\Seeders;

use App\Models\Gym;
use Illuminate\Database\Seeder;


class GymsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Gym::factory()->count(30)->create();

    }
}
