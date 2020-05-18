<?php

$router->group([
    'prefix' => 'project-event-estimations',
    'namespace' => '\Modules\ProjectEventEstimations\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'project-event-estimations.list',
        'schema'      => '/v1/project-event-estimations/project-event-estimation.json',
        'description' => 'Get project event estimations list',
        'uses'        => 'ProjectEventEstimationsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'project-event-estimations.one',
        'schema'      => '/v1/project-event-estimations/project-event-estimation.json',
        'description' => 'Get single project event vestimation',
        'uses'        => 'ProjectEventEstimationsController@getOne'
    ]);
});
