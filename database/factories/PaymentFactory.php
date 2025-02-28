<?php

// database/factories/PaymentFactory.php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PaymentFactory extends Factory
{
    protected $model = \App\Models\Payment::class;

    public function definition()
    {
        return [
            'client_id' => 1,  // Pick a random client
            'plan_id' => 1,  // Pick a random plan
            'payment_date' => Carbon::now(),  // Payment date set to the current time
        ];
    }
}
