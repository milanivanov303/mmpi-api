<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
     * @return true|null
     */
    protected function basicAuth($credentials)
    {
        try {
            list($user, $pass) = explode(':', base64_decode($credentials));
            return User::auth($user, $pass);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }

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
    }

    /**
     * Token authentication
     *
     * @param string $token
     * @return bool
     */
    protected function tokenAuth($token)
    {
        // hardcore this for now till we have clear idea how tokens will work
        if ($token === 'GOSTUN-19751019-ORGANA') {
            return new User();
        }
        
        return null;
        
        // implement logic for token auth
        // return User::where('api_token', $token)->first();
    }
}
