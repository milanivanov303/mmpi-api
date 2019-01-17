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
    $router->get('/{id}', [
        'as'          => 'modifications.one',
        'schema'      => '/v1/modifications/modification.json',
        'description' => 'Get single modification',
        'uses'        => 'ModificationsController@getOne'
    ]);
});
