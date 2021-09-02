<?php

$router->group([
    'prefix' => 'isabs',
    'namespace' => '\Modules\Isabs\Http\Controllers'
], function () use ($router) {
    $router->get('/login', [
        'description' => 'List tech names from isabs api',
        'uses'        => 'IsabsController@login'
    ]);
    $router->get('/specs', [
        'description' => 'List tech names from isabs api',
        'uses'        => 'IsabsController@getSpecs'
    ]);
});
