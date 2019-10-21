<?php

$router->group([
    'prefix' => 'project-event-estimations',
    'namespace' => '\Modules\PojectEventEstimations\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'project-event-estimations.list',
        'schema'      => '/v1/project-event-estimations/project-event-estimation.json',
        'description' => 'Get poject event estimations list',
        'uses'        => 'PojectEventEstimationsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'project-event-estimations.one',
        'schema'      => '/v1/project-event-estimations/project-event-estimation.json',
        'description' => 'Get single poject event estimation',
        'uses'        => 'PojectEventEstimationsController@getOne'
    ]);
    $router->post('', [
        'as'          => 'project-event-estimations.create',
        'schema'      => '/v1/project-event-estimations/create.json',
        'description' => 'Create new poject event estimation',
        'uses'        => 'PojectEventEstimationsController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'project-event-estimations.update',
        'description' => 'Update poject event estimation',
        'schema'      => '/v1/project-event-estimations/update.json',
        'uses'        => 'PojectEventEstimationsController@update'
    ]);
    $router->delete('/{id}', [
        'as'          => 'project-event-estimations.delete',
        'description' => 'Delete poject event estimation',
        'uses'        => 'PojectEventEstimationsController@delete'
    ]);
});
