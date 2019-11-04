<?php

namespace Modules\Artifactory\Helpers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class Request
{
    /**
     * Request url
     *
     * @var string
     */
    protected $url;

    /**
     * Request method
     *
     * @var string
     */
    protected $method;

    /**
     * Request headers
     *
     * @var array
     */
    protected $headers;

    /**
     * Request constructor.
     *
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $headers
     */
    public function __construct(
        string $url,
        string $method,
        array $headers = []
    ) {
        $this->url     = $url;
        $this->method  = $method;
        $this->headers = $headers;
    }

    /**
     * Get url
     *
     * @return string
     */
    protected function getUrl()
    {
        return $this->url;
    }

    /**
     * Send request
     *
     * @return Response
     */
    public function send() : Response
    {
        try {
            $client = new \GuzzleHttp\Client();

            $options = [
                'headers' => $this->headers,
                'verify'  => false
            ];

            $response = $client->request($this->method, $this->url, $options);

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
