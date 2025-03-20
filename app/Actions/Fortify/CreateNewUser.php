<?php

namespace App\Actions\Fortify;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        // Create the user
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Automatically create a unique gym for the new user
        // Using the user's name as the gym name with a unique identifier
        $gymName = $input['name'] . "'s Gym";
        $gym = Gym::create([
            'name' => $gymName,
            'location' => $input['location'] ?? 'Default Location',
            'user_id' => $user->id,  // Set the user_id to the authenticated user's ID
        ]);

        // Assign the user to the newly created gym
        $user->gyms()->attach($gym->id);
        
        Log::info("Auto-created gym '{$gymName}' (ID: {$gym->id}) for new user {$user->id}");

        return $user;
    }
}
