<?php

$router->group([
    'prefix' => 'patch-requests',
    'namespace' => '\Modules\PatchRequests\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'patch-requests.list',
        'schema'      => '/v1/patch-requests/patch-request.json',
        'description' => 'Get patch requests list',
        'uses'        => 'PatchRequestsController@getMany'
    ]);
    $router->get('/{tts_id}', [
        'as'          => 'patch-requests.one',
        'schema'      => '/v1/patch-requests/patch-request.json',
        'description' => 'Get single patch request',
        'uses'        => 'PatchRequestsController@getOne'
    ]);
    $router->post('', [
        'as'          => 'patch-requests.create',
        'schema'      => '/v1/patch-requests/patch-request.json',
        'description' => 'Create new patch request',
        'uses'        => 'PatchRequestsController@create'
    ]);
    $router->put('/{tts_id:[A-Z-0-9]+}', [
        'as'          => 'patch-requests.update',
        'description' => 'Update patch request',
        'schema'      => '/v1/patch-requests/patch-request.json',
        'uses'        => 'PatchRequestsController@update'
    ]);
    $router->delete('/{tts_id:[A-Z-0-9]+}', [
        'as'          => 'patch-requests.delete',
        'description' => 'Delete patch request',
        'uses'        => 'PatchRequestsController@delete'
    ]);
});
