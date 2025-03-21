<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    public function run()
    {
        Expense::create([
            'gym_id' => 1, 
            'description' => 'Electricity Bill', 
            'amount' => 100, 
            'bill_date' => '2025-01-01',
            'status' => 'pending',
            'due_date' => '2025-01-15',
            'category' => 'utilities',
            'type' => 'bill'
        ]);
        
        Expense::create([
            'gym_id' => 2, 
            'description' => 'Water Bill', 
            'amount' => 50, 
            'bill_date' => '2025-01-15',
            'status' => 'pending',
            'due_date' => '2025-01-30',
            'category' => 'water',
            'type' => 'bill'
        ]);
    }
}
