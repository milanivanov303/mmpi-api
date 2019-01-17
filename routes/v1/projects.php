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
});
