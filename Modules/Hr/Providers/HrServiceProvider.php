<?php

namespace Modules\Hr\Providers;

use Illuminate\Support\ServiceProvider;

class HrServiceProvider extends ServiceProvider
{
    /**
     * Register issues services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('HRApi', function () {
            return app('ApiSdk')->api(config('app.hr.url') . '/v1', config('app.hr.code'));
        });
    }
}
