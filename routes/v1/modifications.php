<?php

$router->group([
    'prefix' => 'modifications',
    'namespace' => '\Modules\Modifications\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'modifications.list',
        'schema'      => '/v1/modifications/modification.json',
        'description' => 'Get modifications list',
        'uses'        => 'ModificationsController@getMany'
    ]);
    $router->get('/{id:[0-9]+}', [
        'as'          => 'modifications.one',
        'schema'      => '/v1/modifications/modification.json',
        'description' => 'Get single modification',
        'uses'        => 'ModificationsController@getOne'
    ]);

    $router->group(['prefix' => 'sources'], function () use ($router) {
        $router->get('', [
            'as'          => 'modifications.sources.list',
            'schema'      => '/v1/modifications/sources/source.json',
            'description' => 'Get sources modifications list',
            'uses'        => 'ModificationsController@getMany'
        ]);
        $router->get('/{id:[0-9]+}', [
            'as'          => 'modifications.sources.one',
            'schema'      => '/v1/modifications/sources/source.json',
            'description' => 'Get single source modification',
            'type'        => 'source',
            'uses'        => 'ModificationsController@getOne'
        ]);
        $router->post('', [
            'as'          => 'modifications.sources.create',
            'schema'      => '/v1/modifications/sources/create.json',
            'description' => 'Attach source modification',
            'type'        => 'source',
            'uses'        => 'ModificationsController@create'
        ]);
        $router->put('{id:[0-9]+}', [
            'as'          => 'modifications.sources.update',
            'schema'      => '/v1/modifications/sources/update.json',
            'description' => 'Update source modification',
            'type'        => 'source',
            'uses'        => 'ModificationsController@update'
        ]);
    });
});
