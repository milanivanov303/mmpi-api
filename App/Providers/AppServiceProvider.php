<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('elastic', function ($app) {
            return ClientBuilder::create()
                ->setHosts(config('app.elastic.hosts'))
                ->build();
        });
    }
}
