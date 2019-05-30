<?php

$router->group([
    'prefix' => 'project-specifics',
    'namespace' => '\Modules\ProjectSpecifics\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'project-specifics.list',
        'schema'      => '/v1/project-specifics/project-specific.json',
        'description' => 'Get project specifics list',
        'uses'        => 'ProjectSpecificsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'project-specifics.one',
        'schema'      => '/v1/project-specifics/project-specific.json',
        'description' => 'Get single project specific',
        'uses'        => 'ProjectSpecificsController@getOne'
    ]);
    $router->post('', [
        'as'          => 'project-specifics.create',
        'schema'      => '/v1/project-specifics/create.json',
        'description' => 'Create new project specific',
        'uses'        => 'ProjectSpecificsController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'project-specifics.update',
        'description' => 'Update project specifics',
        'schema'      => '/v1/project-specifics/update.json',
        'uses'        => 'ProjectSpecificsController@update'
    ]);
    $router->delete('/{id}', [
        'as'          => 'project-specifics.delete',
        'description' => 'Delete project specifics',
        'uses'        => 'ProjectSpecificsController@delete'
    ]);
});
