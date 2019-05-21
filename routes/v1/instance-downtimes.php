<?php

$router->group([
    'prefix' => 'instance-downtimes',
    'namespace' => '\Modules\InstanceDowntimes\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'instance-downtimes.list',
        'schema'      => '/v1/instance-downtimes/instance-downtime.json',
        'description' => 'Get instance downtimes list',
        'uses'        => 'InstanceDowntimesController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'instance-downtimes.one',
        'schema'      => '/v1/instance-downtimes/instance-downtime.json',
        'description' => 'Get single instance downtime',
        'uses'        => 'InstanceDowntimesController@getOne'
    ]);
    $router->post('', [
        'as'          => 'instance-downtimes.create',
        'schema'      => '/v1/instance-downtimes/create.json',
        'description' => 'Create new instance downtime',
        'uses'        => 'InstanceDowntimesController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'instance-downtimes.update',
        'description' => 'Update instance downtimes',
        'schema'      => '/v1/instance-downtimes/update.json',
        'uses'        => 'InstanceDowntimesController@update'
    ]);
    $router->delete('/{id}', [
        'as'          => 'instance-downtimes.delete',
        'description' => 'Delete instance downtimes',
        'uses'        => 'InstanceDowntimesController@delete'
    ]);
});
