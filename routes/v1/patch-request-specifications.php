<?php

$router->group([
    'prefix' => 'patch-request-specifications',
    'namespace' => '\Modules\PatchRequestSpecifications\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'patch-request-specifications.list',
        //'schema'      => '/v1/project-specifics/project-specific.json',
        'description' => 'Get patch request specifications list',
        'uses'        => 'PatchRequestSpecificationsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'patch-request-specifications.one',
        //'schema'      => '/v1/project-specifics/project-specific.json',
        'description' => 'Get single patch request specification',
        'uses'        => 'PatchRequestSpecificationsController@getOne'
    ]);
    $router->post('', [
        'as'          => 'patch-request-specifications.create',
        //'schema'      => '/v1/project-specifics/create.json',
        'description' => 'Create new patch request specification',
        'uses'        => 'PatchRequestSpecificationsController@create'
    ]);
    $router->delete('/{id}', [
        'as'          => 'patch-request-specifications.delete',
        'description' => 'Delete patch request specification',
        'uses'        => 'PatchRequestSpecificationsController@delete'
    ]);
});
