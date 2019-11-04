<?php

namespace Modules\Artifactory\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Artifactory\Helpers\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class ArtifactoryController extends BaseController
{
     /**
     * API url
     *
     * @var string
     */
    protected $url;

    /**
     * Make get request
     *
     * @param string $uri
     * @param array $data
     * @return Response
     */
    public function execute(string $uri) : Response
    {
        return $this->send('GET', $this->getUrl($uri));
    }

    /**
     * Send request
     *
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return Response
     */
    protected function send(string $method, string $url, array $headers = []) : Response
    {
        $headers = array_merge(
            $this->getDefaultHeaders(),
            $headers
        );

        $request = new Request($url, $method, $headers);

        return $request->send();
    }

    /**
     * Get request default headers
     *
     * @return array
     */
    protected function getDefaultHeaders() : array
    {
        $headers = [
            'X-JFrog-Art-Api' => config('app.artifactory.key')
        ];

        return $headers;
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
}
