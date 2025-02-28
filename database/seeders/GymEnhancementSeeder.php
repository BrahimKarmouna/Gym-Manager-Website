<?php

namespace Database\Seeders;

use App\Models\GymEnhancement;
use Illuminate\Database\Seeder;

class GymEnhancementSeeder extends Seeder
{
    public function run()
    {
        GymEnhancement::create(['gym_id' => 1, 'description' => 'New Treadmill', 'cost' => 1500, 'enhancement_date' => '2025-01-10']);
        GymEnhancement::create(['gym_id' => 2, 'description' => 'Added Mirrors', 'cost' => 500, 'enhancement_date' => '2025-01-20']);
    }
}
