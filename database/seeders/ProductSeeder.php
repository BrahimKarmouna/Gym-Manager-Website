<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create(['gym_id' => 1, 'name' => 'Protein Shake', 'price' => 30, 'stock' => 50]);
        Product::create(['gym_id' => 2, 'name' => 'Gym Towel', 'price' => 10, 'stock' => 100]);
    }
}
