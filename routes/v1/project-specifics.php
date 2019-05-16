<?php

$router->group([
    'prefix' => 'project-specifics',
    'namespace' => '\Modules\ProjectSpecifics\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'project-specifics.list',
        'schema'      => '/v1/project-specifics/project-specific.json',
        'description' => 'Get instance downtimes list',
        'uses'        => 'ProjectSpecificsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'project-specifics.one',
        'schema'      => '/v1/project-specifics/project-specific.json',
        'description' => 'Get single instance downtime',
        'uses'        => 'ProjectSpecificsController@getOne'
    ]);
    $router->post('', [
        'as'          => 'project-specifics.create',
        'schema'      => '/v1/project-specifics/create.json',
        'description' => 'Create new instance downtime',
        'uses'        => 'ProjectSpecificsController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'project-specifics.update',
        'description' => 'Update instance downtimes',
        'schema'      => '/v1/project-specifics/update.json',
        'uses'        => 'ProjectSpecificsController@update'
    ]);
    $router->delete('/{id}', [
        'as'          => 'project-specifics.delete',
        'description' => 'Delete instance downtimes',
        'uses'        => 'ProjectSpecificsController@delete'
    ]);
});
