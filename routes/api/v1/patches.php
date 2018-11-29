<?php

$router->group([
    'prefix' => 'patches',
    'namespace' => '\Modules\Patches\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'patches.list',
        'schema'      => '/api/v1/patches/patch.json',
        'description' => 'Get patches list',
        'uses'        => 'PatchesController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'patch.one',
        'schema'      => '/api/v1/patches/patch.json',
        'description' => 'Get single patch',
        'uses'        => 'PatchesController@getOne'
    ]);
});
