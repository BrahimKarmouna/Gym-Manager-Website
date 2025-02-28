<?php

namespace Database\Seeders;

use App\Models\Bill;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    public function run()
    {
        Bill::create(['gym_id' => 1, 'description' => 'Electricity Bill', 'amount' => 100, 'bill_date' => '2025-01-01']);
        Bill::create(['gym_id' => 2, 'description' => 'Water Bill', 'amount' => 50, 'bill_date' => '2025-01-15']);
    }
}
