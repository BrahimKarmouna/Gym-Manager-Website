<?php

// database/seeders/PaymentSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        // Create 30 fake payment records
        Payment::factory()->count(1)->create();
    }
}
