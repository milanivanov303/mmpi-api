<?php

$router->group([
    'prefix' => 'modifications',
    'namespace' => '\Modules\Modifications\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'modifications.list',
        'schema'      => '/api/v1/modifications/modification.json',
        'description' => 'Get modifications list',
        'uses'        => 'ModificationsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'modifications.one',
        'schema'      => '/api/v1/modifications/modification.json',
        'description' => 'Get single modification',
        'uses'        => 'ModificationsController@getOne'
    ]);
});
