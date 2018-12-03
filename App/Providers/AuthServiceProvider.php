<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends \Core\Providers\AuthServiceProvider
{
    /**
     * Register policies.
     */
    public function registerPolicies()
    {
        Gate::policy(User::class, UserPolicy::class);
    }
}
