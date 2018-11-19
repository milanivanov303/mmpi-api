<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use App\Helpers\Ldap;
use Adldap\Adldap;
use Firebase\JWT\JWT;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function (Request $request) {
            $header = $request->header('Authorization');
            if ($header) {
                list($type, $credentials) = explode(' ', $header);

                if ($type === 'Basic') {
                    return $this->basicAuth($credentials);
                } elseif ($type === 'Digest') {
                    return $this->digestAuth($credentials);
                } elseif ($type === 'Bearer') {
                    return $this->tokenAuth($credentials);
                }
            }

            $token = $request->header('X-AUTH-TOKEN');
            if ($token) {
                return $this->tokenAuth($token);
            }

            return null;
        });
    }

    /**
     * Basic authentication
     *
     * @param string $credentials Base64 encoded credentials
     * @return User|null
     */
    protected function basicAuth($credentials)
    {
        /*
        list($username, $password) = explode(':', base64_decode($credentials));

        try {
            $ldap = new Ldap(
                new Adldap(['default' => config('app.ldap')])
            );
            return $ldap->auth($username, $password);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        */
        return null;
    }

    /**
     * Digest authentication
     *
     * @param string $credentials Base64 encoded credentials
     * @return bool
     */
    protected function digestAuth($credentials)
    {
        // implement logic for Digest auth
        return null;
    }

    /**
     * Token authentication
     *
     * @param string $token
     * @return bool
     */
    protected function tokenAuth($token)
    {
        try {
            $decoded = JWT::decode(
                $token,
                config('app.jwt.secret_key'),
                [config('app.jwt.algorithm')]
            );
            return User::where('email', $decoded->email)->first();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        
        return null;
    }
}
