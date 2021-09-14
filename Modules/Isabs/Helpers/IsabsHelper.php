<?php

namespace Modules\Isabs\Helpers;

use GuzzleHttp\Client;
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
     * Username
     *
     * @var string
     */
    protected $username;


    /**
     * Password
     *
     * @var string
     */
    protected $password;

    /**
     * Isabs Helper constructor.
     */
    public function __construct()
    {
        $this->url = config('app.isabs.url');
        $this->username = config('app.isabs.username');
        $this->password = config('app.isabs.password');
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function getUrl(string $uri) : string
    {
        return "{$this->url}/{$uri}";
    }

    /**
     * Send request
     *
     * @param string $method
     * @param string $url
     * @param array $options
     * @return Response
     */
    protected function send(string $method, string $url, array $options) : Response
    {
        try {
            $client = new Client();
            $response = $client->request($method, $url, $options);

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

    /**
     * Login in ISABS and get token
     *
     * @return Response
     */
    public function login() : Response
    {
        $response = $this->send('POST', $this->getUrl('login'), [
            'verify'  => false,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body'    => json_encode([
                "login"    => $this->username,
                "password" => $this->password
            ])
        ]);

        $content = json_decode($response->content());

        if ($response->getStatusCode() === 200 && is_object($content) && property_exists($content, 'token')) {
            return $response;
        }

        return Response::create('Could not get token', 424);
    }

    public function specifications($token)
    {
        return $this->send('GET', $this->getUrl('list-tech-names'), [
            'verify'  => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '. $token
            ]
        ]);
    }
}
