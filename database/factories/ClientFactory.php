<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\Gym;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'gym_id' => Gym::inRandomOrder()->first()->id ?? Gym::factory(), // Random gym or create one
            'Full_name' => $this->faker->name,
            'date_of_birth' => $this->faker->date(),
            'address' => $this->faker->address,
            'id_card_picture' => 'clients/aF70UqsvtUaRTeN2c5PXtV0p3NIxuTy8Px3dbGBV.png', // Fixed path
            'client_picture' => 'clients/aF70UqsvtUaRTeN2c5PXtV0p3NIxuTy8Px3dbGBV.png', // Fixed path
            'id_card_number' => $this->faker->unique()->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            // 'is_assured' => 0,
            // 'is_payed' => 0,
            'subscription_expired_date' => now(), // Subscription expires in 6 months
            'assurance_expired_date' => now(), // Assurance expires in 1 year
        ];
    }
}
