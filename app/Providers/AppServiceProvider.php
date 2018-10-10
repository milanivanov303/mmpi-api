<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\HashRepository', function($app) {
            return new \App\Repositories\EloquentHashRepository( new \App\Models\Hashes\HashCommit );
        });
    }
}
