<?php

namespace Modules\Gitlab\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class GitlabHelper
{
    /**
     * Key of file path from gitlab api response
     * @var string
     */
    protected $filePath = 'new_path';

    public function getCommitFilesWithCurl(array $config, string $repo, string $sha)
    {
        $curlResponse = self::commitFilesWithCurl($config, $repo, $sha)->getContent();
        preg_match_all('/(?<="' . $this->filePath . '":)"(.*?)"/', $curlResponse, $matches);

        $files = [];
        foreach ($matches[1] as $file) {
            $files[] = [$this->filePath => $file];
        }

        return $files;
    }

    public function commitFilesWithCurl(array $config, string $repo, string $sha) : Response
    {
        $hostname = parse_url($config['repoUrl'], PHP_URL_HOST);
        $repoTokens = config("app.repo-tokens");
        $token = array_key_exists($hostname, $repoTokens) ? $repoTokens[$hostname] : null;
        $repo = urlencode($repo);

        try {
            $client = new Client();
            $options = ['verify' => false];
            $response = $client->request(
                'GET',
                "{$config['repoUrl']}/api/v4/projects/{$repo}/repository/commits/{$sha}/diff?private_token={$token}",
                $options
            );

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
