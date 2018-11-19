<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\Ldap;
use Adldap\Adldap;
use Firebase\JWT\JWT;
use App\Models\User;

class AuthController extends BaseController
{
    /**
     * Authenticate user and return JWT
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function auth(Request $request, Response $response)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        try {
            $ldap = new Ldap(
                new Adldap(['default' => config('app.ldap')])
            );

            if ($user = $ldap->auth($username, $password)) {
                return $response->header('X-AUTH-TOKEN', $this->getJWT($user));
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $response->setStatusCode(401);
    }

    /**
     * Get JWT
     *
     * @param User $user
     *
     * @return string
     */
    protected function getJWT($user)
    {
        $token = [
            'iat'   => time(),
            'exp'   => strtotime(config('app.jwt.exp')),
            'name'  => $user->name,
            'email' => $user->email
        ];

        return JWT::encode(
            $token,
            config('app.jwt.secret_key'),
            config('app.jwt.algorithm')
        );
    }
}
