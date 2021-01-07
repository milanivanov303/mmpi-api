<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Core\Helpers\SSH2;

class SSHServiceProvider extends ServiceProvider
{
    /**
     * Register app services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ssh2', function ($app, $data) {
            $username  = config('app.ssh.username');
            $password  = config('app.ssh.password');
            $port      = config('app.ssh.port');
            $publicKey = Storage::get(config('app.ssh.public_key'));
            $host      = strpos($data['instance'], '.codixfr.private')
                         ? $data['instance']
                         : $data['instance'] . '.codixfr.private';

            $ssh2 = new SSH2($host, $port);

            // login using public key
            if ($publicKey) {
                if (!$ssh2->loginRSA($username, $publicKey)) {
                    if (!$ssh2->login($username, $password)) {
                        throw new \Exception("Could not login to {$host}");
                    }
                }
                return $ssh2;
            }

            // login with password
            if (!$ssh2->login($username, $password)) {
                throw new \Exception("Could not login to {$host}");
            }

            return $ssh2;
        });
    }
}
