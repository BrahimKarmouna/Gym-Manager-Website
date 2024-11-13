<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run(): void
  {
    // Create a user only if it doesn't already exist
    // if (!User::where('email', 'test@example.com')->exists()) {
    //     User::create([
    //         'name' => 'test',
    //         'email' => 'test@example.com',
    //         'password' => bcrypt('password'),
    //     ]);
    // }

    // Call other seeders
    $this->call([
        // TransactionSeeder::class,
        // AccountSeeder::class, // Make sure the AccountSeeder exists and is set up
      CategorySeeder::class,
    ]);
  }
}
