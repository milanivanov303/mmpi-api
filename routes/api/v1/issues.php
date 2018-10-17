<?php

$router->group([
    'prefix' => 'issues',
    'namespace' => '\App\Modules\Issues\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'issues.list',
        'schema'      => '/api/v1/issue.json',
        'description' => 'Get issues list',
        'uses'        => 'IssuesController@getMany'
    ]);
    $router->get('/{tts_id}', [
        'as'          => 'issues.one',
        'schema'      => '/api/v1/issue.json',
        'description' => 'Get single issue',
        'uses'        => 'IssuesController@getOne'
    ]);
    $router->post('', [
        'as'          => 'issues.create',
        'schema'      => '/api/v1/issue.json',
        'description' => 'Create new issue',
        'uses'        => 'IssuesController@create'
    ]);
    $router->put('/{tts_id:[A-Z-0-9]+}', [
        'as'          => 'issues.update',
        'description' => 'Update issue',
        'schema'      => '/api/v1/issue.json',
        'uses'        => 'IssuesController@update'
    ]);
    $router->delete('/{tts_id:[A-Z-0-9]+}', [
        'as'          => 'issues.delete',
        'description' => 'Delete issue',
        'uses'        => 'IssuesController@delete'
    ]);
});
