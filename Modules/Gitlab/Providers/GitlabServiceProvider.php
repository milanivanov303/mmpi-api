<?php

namespace Modules\Gitlab\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as GuzzleHttpClient;
use Gitlab\Client as GitlabClient;

class GitlabServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('GitlabApi', function () {
            $httpClient = new GuzzleHttpClient([
                'verify' => false
            ]);
            
            $client = GitlabClient::createWithHttpClient($httpClient);
            $client->setUrl(config('app.gitlab.url'));
            $client->authenticate(config('app.gitlab.token'), GitlabClient::AUTH_HTTP_TOKEN);
            
            return $client;
        });
    }
}
