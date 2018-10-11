<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Modules\Hashes\Repositories\HashRepository;
use App\Modules\Hashes\Repositories\EloquentHashRepository;
use App\Modules\Hashes\Models\HashCommit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HashRepository::class, function () {
            return new EloquentHashRepository(new HashCommit);
        });
    }
}
