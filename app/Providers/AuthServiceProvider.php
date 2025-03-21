<?php

namespace App\Providers;

use App\Models\Assistant;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Configure Sanctum to authenticate assistants
        Sanctum::authenticateAccessTokensUsing(
            function (PersonalAccessToken $accessToken, bool $is_valid) {
                // Check if the token belongs to an assistant model
                if ($accessToken->tokenable_type === Assistant::class) {
                    return $is_valid;
                }
                
                // Fallback to default behavior for other models
                return $is_valid;
            }
        );
    }
}
