<?php

$router->group([
    'prefix' => 'projects',
    'namespace' => '\Modules\Projects\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'projects.list',
        'schema'      => '/v1/projects/project.json',
        'description' => 'Get projects list',
        'uses'        => 'ProjectsController@getMany'
    ]);
    $router->get('/{name}', [
        'as'          => 'projects.one',
        'schema'      => '/v1/projects/project.json',
        'description' => 'Get single projects',
        'uses'        => 'ProjectsController@getOne'
    ]);
    $router->post('', [
        'as'          => 'projects.create',
        'schema'      => '/v1/projects/create.json',
        'description' => 'Create new project',
        'uses'        => 'ProjectsController@create'
    ]);
    $router->get('/export/{type}', [
        'as'          => 'projects.export',
        'schema'      => '/v1/projects/project.json',
        'description' => 'Import project events',
        'uses'        => 'ProjectsController@export'
    ]);
    $router->put('/roles-tmp/{id}', [
        'as'          => 'roles-tmp.update',
        'description' => 'Update temporary project user roles',
        'uses'        => 'ProjectsController@updateRolesTmp'
    ]);
    $router->put('/roles/{id}', [
        'as'          => 'roles.update',
        'description' => 'Update project user roles',
        'uses'        => 'ProjectsController@updateRoles'
    ]);
    $router->put('/{name}', [
        'as'          => 'projects.update',
        'description' => 'Update project',
        'schema'      => '/v1/projects/update.json',
        'uses'        => 'ProjectsController@update'
    ]);
    $router->delete('/{name}', [
        'as'          => 'projects.delete',
        'description' => 'Delete project',
        'uses'        => 'ProjectsController@delete'
    ]);
});
