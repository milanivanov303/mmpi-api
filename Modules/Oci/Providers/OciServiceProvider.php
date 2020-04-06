<?php

namespace Modules\Oci\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use phpseclib\Net\SFTP;

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

        $this->app->singleton('GetTnsnameora', function () {
            $host     = config('app.ssh.tnsname_host');
            $port     = config('app.ssh.port');
            $username = config('app.ssh.username');
            $password = config('app.ssh.password');

            $sftp = new SFTP($host, $port, 30);
            if (!$sftp->login($username, $password)) {
                Log::error("Could not login to instance {$host}");
                throw new \Exception("Could not login to instance {$host}");
            }
            return $sftp;
        });
    }
}
