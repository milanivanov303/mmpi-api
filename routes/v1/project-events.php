<?php

$router->group([
    'prefix' => 'project-events',
    'namespace' => '\Modules\ProjectEvents\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'project-events.list',
        'schema'      => '/v1/project-events/project-event.json',
        'description' => 'Get project events list',
        'uses'        => 'ProjectEventsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'project-events.one',
        'schema'      => '/v1/project-events/project-event.json',
        'description' => 'Get single project event',
        'uses'        => 'ProjectEventsController@getOne'
    ]);
    $router->post('', [
        'as'          => 'project-events.create',
        'schema'      => '/v1/project-events/create.json',
        'description' => 'Create new project event',
        'uses'        => 'ProjectEventsController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'project-events.update',
        'description' => 'Update project events',
        'schema'      => '/v1/project-events/update.json',
        'uses'        => 'ProjectEventsController@update'
    ]);
    // $router->delete('/{id}', [
    //     'as'          => 'installations.delete',
    //     'description' => 'Delete installation',
    //     'uses'        => 'InstallationsController@delete'
    // ]);
});
