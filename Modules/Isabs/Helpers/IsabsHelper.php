<?php

namespace Modules\Isabs\Helpers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class IsabsHelper
{
    /**
     * Request url
     *
     * @var string
     */
    protected $url;

    /**
     * Isabs Helper constructor.
     */
    public function __construct()
    {
        $this->url = config('app.isabs.url');
    }

    /**
     * Login in ISABS and get token
     *
     * @return Response
     */
    public function login() : Response
    {
        try {
            $client = new \GuzzleHttp\Client();

            $options = [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body'    => json_encode([
                    "login" => config('app.isabs.username'),
                    "password" => config('app.isabs.password')
                ])
            ];

            $response = $client->request('POST', $this->url . '/login', $options);

            return Response::create(
                (string) $response->getBody(),
                $response->getStatusCode(),
                [
                    'Content-Type' => 'application/json'
                ]
            );
        } catch (GuzzleException $e) {
            if ($e->getResponse()) {
                return Response::create(
                    (string) $e->getResponse()->getBody(),
                    $e->getResponse()->getStatusCode(),
                    [
                        'Content-Type' => 'application/json'
                    ]
                );
            }

            return Response::create('Bad Request', 400);
        }
    }

    public function specifications($token)
    {
        try {
            $client = new \GuzzleHttp\Client();

            $options = [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '. $token,
                ]
            ];

            $response = $client->request('GET', $this->url . '/list-tech-names', $options);

            return Response::create(
                (string) $response->getBody(),
                $response->getStatusCode(),
                [
                    'Content-Type' => 'application/json'
                ]
            );
        } catch (GuzzleException $e) {
            if ($e->getResponse()) {
                return Response::create(
                    (string) $e->getResponse()->getBody(),
                    $e->getResponse()->getStatusCode(),
                    [
                        'Content-Type' => 'application/json'
                    ]
                );
            }

            return Response::create('Bad Request', 400);
        }
    }
}
