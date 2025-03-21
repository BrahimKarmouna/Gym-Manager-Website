<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gym;
use App\Models\Client;
use App\Models\Plan;
use App\Models\InsurancePlan;
use App\Models\Member;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    // Call role and permission seeder first
    $this->call([
      RolePermissionSeeder::class,
    ]);

    // Create 5 users
    $users = User::factory(5)->create();

    // For each user, create related data
    foreach ($users as $user) {
      // Create 2 gyms for each user
      $gyms = [];
      for ($i = 0; $i < 2; $i++) {
        $gyms[] = Gym::create([
          'name' => fake()->company() . ' Gym',
          'user_id' => $user->id,
          'location' => fake()->address(),
        ]);
      }

      // Create 2 plans for each user
      $plans = [];
      $plans[] = Plan::create([
        'name' => 'Monthly Plan',
        'price' => fake()->numberBetween(30, 100),
        'duration' => '1',
        'user_id' => $user->id,
      ]);
      $plans[] = Plan::create([
        'name' => 'Yearly Plan',
        'price' => fake()->numberBetween(300, 1000),
        'duration' => '12',
        'user_id' => $user->id,
      ]);

      // Create 2 insurance plans for each user
      $insurancePlans = [];
      $insurancePlans[] = InsurancePlan::create([
        'name' => 'Basic Insurance',
        'price' => fake()->numberBetween(20, 50),
        'duration' => 6,
        'user_id' => $user->id,
      ]);
      $insurancePlans[] = InsurancePlan::create([
        'name' => 'Premium Insurance',
        'price' => fake()->numberBetween(50, 100),
        'duration' => 12,
        'user_id' => $user->id,
      ]);

      // Create 5 clients for each user
      $clients = [];
      for ($i = 0; $i < 5; $i++) {
        $clients[] = Client::create([
          'gym_id' => $gyms[array_rand($gyms)]->id,
          'Full_name' => fake()->name(),
          'date_of_birth' => fake()->date(),
          'address' => fake()->address(),
          'id_card_picture' => 'default/id_card.jpg',
          'client_picture' => 'default/client.jpg',
          'id_card_number' => fake()->numerify('ID-########'),
          'email' => fake()->unique()->safeEmail(),
          'phone' => fake()->phoneNumber(),
          'subscription_expired_date' => fake()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
          'assurance_expired_date' => fake()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
          'user_id' => $user->id,
        ]);
      }

      // Create 3 members for each user (linked to gyms, plans, and insurance plans)
      for ($i = 0; $i < 3; $i++) {
        Member::create([
          'gym_id' => $gyms[array_rand($gyms)]->id,
          'name' => fake()->name(),
          'email' => fake()->unique()->safeEmail(),
          'phone' => fake()->phoneNumber(),
          'plan_id' => $plans[array_rand($plans)]->id,
          'insurance_plan_id' => $insurancePlans[array_rand($insurancePlans)]->id,
        ]);
      }

      // Create 3 products for each user
      $products = [];
      for ($i = 0; $i < 3; $i++) {
        $products[] = Product::create([
          'gym_id' => $gyms[array_rand($gyms)]->id,
          'name' => fake()->word() . ' ' . fake()->randomElement(['Supplement', 'Equipment', 'Apparel']),
          'price' => fake()->numberBetween(10, 200),
          'stock' => fake()->numberBetween(1, 50),
        ]);
      }

      // Create 3 payments for each user
      for ($i = 0; $i < 3; $i++) {
        Payment::create([
          'client_id' => $clients[array_rand($clients)]->id,
          'plan_id' => $plans[array_rand($plans)]->id,
          'payment_date' => fake()->dateTimeThisYear()->format('Y-m-d'),
          'user_id' => $user->id,
        ]);
      }

      // Create 2 expenses for each user
      for ($i = 0; $i < 2; $i++) {
        Expense::create([
          'gym_id' => $gyms[array_rand($gyms)]->id,
          'description' => fake()->sentence(),
          'amount' => fake()->numberBetween(100, 1000),
          'bill_date' => fake()->dateTimeThisYear()->format('Y-m-d'),
          'status' => fake()->randomElement(['pending', 'paid', 'overdue']),
          'due_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
          'category' => fake()->randomElement(array_keys(Expense::CATEGORIES)),
          'type' => 'bill',
          'user_id' => $user->id,
        ]);
      }
    }
  }
}
