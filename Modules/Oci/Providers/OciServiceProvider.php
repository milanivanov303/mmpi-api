<?php

namespace Modules\Oci\Providers;

use Illuminate\Support\ServiceProvider;

class OciServiceProvider extends ServiceProvider
{
    /**
     * Register issues services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('OciConnect', function ($app, $instance) {
            $username  = $instance['db_user'] ?? config('app.oci.username');
            $password  = config('app.oci.password');
            $tnsname   = $instance['tns_name'];
                         
            $oci = oci_connect($username, $password, $tnsname);
            if (!$oci) {
                $e = oci_error();
                throw new \Exception("Connection ERROR: {$e}");
            }

            return $oci;
        });
    }
}
