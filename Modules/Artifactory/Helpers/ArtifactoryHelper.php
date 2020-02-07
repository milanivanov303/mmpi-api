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
     * Requested file name
     *
     * @var array
     */
    protected $name;

    /**
     * Artifactory Helper constructor.
     *
     * @param string $name
     * @param string $method
     * @param array $data
     * @param array $headers
     */
    public function __construct(string $name, string $method, array $headers)
    {
        $this->url     = config('app.artifactory.url');
        $this->name    = $name;
        $this->method  = $method;
        $this->headers = $headers;
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

            // return only -bin.zip files and only files from libs-snapshot-local repo
            $options = [
                'headers' => $this->headers,
                'verify'  => false,
                'body'    =>
                'items.find({"repo":"libs-snapshot-local","$and":[{"name":{"$match":"*-bin.zip"}},{"name":{"$match":"*'
                    . $this->name
                    .'*"}}]})'
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
