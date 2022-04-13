<?php

$router->group([
    'prefix' => 'instance-types',
    'namespace' => '\Modules\InstanceTypes\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'instance-types.list',
        'description' => 'Get instance types',
        'uses'        => 'InstanceTypesController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'instance-types.one',
        'description' => 'Get specific instance type',
        'uses'        => 'InstanceTypesController@getOne'
    ]);
});
