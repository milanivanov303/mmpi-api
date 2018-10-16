<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Modules\Hashes\ {
    Repositories\HashRepository,
    Repositories\EloquentHashRepository,
    Models\HashCommit
};

use App\Modules\Users\ {
    Repositories\UserRepository,
    Repositories\EloquentUserRepository
};
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepository::class, function () {
            return new EloquentUserRepository(new User);
        });

         $this->app->bind(HashRepository::class, function () {
            return new EloquentHashRepository(new HashCommit);
        });
    }
}
