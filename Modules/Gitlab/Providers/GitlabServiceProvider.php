<?php

namespace Modules\Gitlab\Providers;

use Illuminate\Support\ServiceProvider;

class GitlabServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('GitlabApi', function () {
            $httpClient = new \GuzzleHttp\Client([
                'verify' => false
            ]);
            
            $client = \Gitlab\Client::createWithHttpClient($httpClient);
            $client->setUrl(config('app.gitlab.url'));
            $client->authenticate(config('app.gitlab.token'), \Gitlab\Client::AUTH_HTTP_TOKEN);
            
            return $client;
        });
    }
}
