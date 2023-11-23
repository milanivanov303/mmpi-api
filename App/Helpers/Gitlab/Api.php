<?php

namespace App\Helpers\Gitlab;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class Api
{
    protected string $url;

    protected ?string $token;

    public function __construct(string $url, string $token)
    {
        $this->url   = $url;
        $this->token = $token;
    }

    protected function getUrl(string $uri) : string
    {
        // if there is host in uri do not append url
        if (parse_url($uri, PHP_URL_HOST)) {
            return $uri;
        }

        return "{$this->url}/api/v4/{$uri}";
    }

    protected function send(
        string $method,
        string $url,
        array $options = []
    ) : Response {
        $options['headers'] = array_merge($this->getHeaders(), $options['headers'] ?? []);

        try {
            $client = new GuzzleClient(['verify'  => false]);
            $response = $client->request($method, $url, $options);
        } catch (GuzzleException $e) {
            return new Response($e->getMessage(), 400);
        }

        return new Response(
            $response->getBody()->getContents(),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }

    protected function getHeaders() : array
    {
        return [
            'PRIVATE-TOKEN' => $this->token,
            'Content-Type' => 'application/json',
            'Accept-Encoding' => 'gzip, deflate, br'
        ];
    }

    public function get(string $uri, array $data = []) : Response
    {
        if (isset($data['headers']) && $data['headers']) {
            return $this->send('GET', $this->getUrl($uri), ['headers' => $data['headers']]);
        }

        return $this->send('GET', $this->getUrl($uri), [
            'query' => $data
        ]);
    }

    public function post(string $uri, array $data = []) : Response
    {
        return $this->send('POST', $this->getUrl($uri), [
            'body' => json_encode($data),
        ]);
    }

    public function delete(string $uri) : Response
    {
        return $this->send('DELETE', $this->getUrl($uri));
    }
}
