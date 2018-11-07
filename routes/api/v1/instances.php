<?php

$router->group([
    'prefix' => 'instances',
    'namespace' => '\App\Modules\Instances\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'instances.list',
        'schema'      => '/api/v1/instances/instance.json',
        'description' => 'Get instance list',
        'uses'        => 'InstancesController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'instances.one',
        'schema'      => '/api/v1/instances/instance.json',
        'description' => 'Get single instance',
        'uses'        => 'InstancesController@getOne'
    ]);
});
