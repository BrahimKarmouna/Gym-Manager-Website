<?php
namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run()
    {
        Member::create([
            'gym_id' => 1,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '123456789',
            'plan_id' => 1,
            'insurance_plan_id' => 1,
        ]);

        Member::create([
            'gym_id' => 2,
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '987654321',
            'plan_id' => 2,
            'insurance_plan_id' => 2,
        ]);
    }
}
