<?php

$router->group([
    'prefix' => 'hashes',
    'namespace' => '\App\Modules\Hashes\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'hashes.list',
        'schema'      => '/api/v1/hash.json',
        'description' => 'Get hashes list',
        'uses'        => 'HashesController@getMany'
    ]);
    $router->get('/{rev:[0-9a-z]+}', [
        'as'          => 'hashes.one',
        'schema'      => '/api/v1/hash.json',
        'description' => 'Get single hash',
        'uses'        => 'HashesController@getOne'
    ]);
    $router->post('', [
            'as'          => 'hashes.create',
            'schema'      => '/api/v1/hash.json',
            'description' => 'Create new hash',
            'uses'        => 'HashesController@create'
    ]);
    $router->put('/{rev:[0-9a-z]+}', [
            'as'          => 'hashes.update',
            'description' => 'Update hash',
            'schema'      => '/api/v1/hash.json',
            'uses'        => 'HashesController@update'
    ]);
    $router->delete('/{rev:[0-9a-z]+}', [
        'as'          => 'hash.delete',
        'description' => 'Delete hash',
        'uses'        => 'HashesController@delete'
    ]);
});
