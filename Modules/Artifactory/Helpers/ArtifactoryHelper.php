<?php

namespace Modules\Artifactory\Helpers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class ArtifactoryHelper
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
     * Artifactory Helper constructor.
     *
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $headers
     */
    public function __construct(string $uri, string $method, array $headers)
    {
        $this->url     = $this->getUrl($uri);
        $this->method  = $method;
        $this->headers = $headers;
    }

    /**
     * Get url
     *
     * @param string $uri
     * @return string
     */
    protected function getUrl(string $uri) : string
    {
        return config('app.artifactory.url'). "/{$uri}";
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
