<?php

$router->group([
    'prefix' => 'projects',
    'namespace' => '\App\Modules\Projects\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'projects.list',
        'schema'      => '/api/v1/projects/project.json',
        'description' => 'Get projects list',
        'uses'        => 'ProjectsController@getMany'
    ]);
    $router->get('/{name}', [
        'as'          => 'projects.one',
        'schema'      => '/api/v1/projects/project.json',
        'description' => 'Get single projects',
        'uses'        => 'ProjectsController@getOne'
    ]);
});
