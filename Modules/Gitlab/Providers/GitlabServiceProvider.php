<?php

namespace Modules\Gitlab\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as GuzzleHttpClient;
use Gitlab\Client as GitlabClient;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Helpers\Gitlab\Api as GitlabApi;

class GitlabServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('NewGitlabApi', function () {
            return new GitlabApi(
                config("app.gitlab.url"),
                config("app.gitlab.token")
            );
        });

        $this->app->bind('GitlabApi', function ($app, $config) {

            $config['headers'] = isset($config['headers']) ? $config['headers'] : [];
            $token = $this->getToken($config['repoUrl']);

            if (is_null($token)) {
                throw new HttpException(401, 'Could not get token from server url');
            }

            $httpClient = new GuzzleHttpClient([
                'verify' => false,
                'headers' => $config['headers']
            ]);
            
            $client = GitlabClient::createWithHttpClient($httpClient);
            $client->setUrl($config['repoUrl']);
            $client->authenticate($token, GitlabClient::AUTH_HTTP_TOKEN);
            
            return $client;
        });
    }

    protected function getToken(string $repoUrl) : ?string
    {
        $hostname = parse_url($repoUrl, PHP_URL_HOST);
        $repoTokens = config("app.repo-tokens");

        return array_key_exists($hostname, $repoTokens) ? $repoTokens[$hostname] : null;
    }
}
