<?php

$router->group([
    'prefix' => 'instances',
    'namespace' => '\Modules\Instances\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'instances.list',
        'schema'      => '/v1/instances/instance.json',
        'description' => 'Get instance list',
        'uses'        => 'InstancesController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'instances.one',
        'schema'      => '/v1/instances/instance.json',
        'description' => 'Get single instance',
        'uses'        => 'InstancesController@getOne'
    ]);
    $router->post('', [
        'as'          => 'instances.create',
        'schema'      => '/v1/instances/create.json',
        'description' => 'Create new instance',
        'uses'        => 'InstancesController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'instances.update',
        'description' => 'Update instance',
        'schema'      => '/v1/instances/update.json',
        'uses'        => 'InstancesController@update'
    ]);
    $router->delete('/{id}', [
        'as'          => 'instances.delete',
        'description' => 'Delete instance',
        'uses'        => 'InstancesController@delete'
    ]);
});
