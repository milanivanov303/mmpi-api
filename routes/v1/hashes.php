<?php

$router->group([
    'prefix' => 'hashes',
    'namespace' => '\Modules\Hashes\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'hashes.list',
        'schema'      => '/v1/hashes/hash.json',
        'description' => 'Get hashes list',
        'uses'        => 'HashesController@getMany'
    ]);
    $router->get('/{hash_rev:[0-9a-z]+}', [
        'as'          => 'hashes.one',
        'schema'      => '/v1/hashes/hash.json',
        'description' => 'Get single hash',
        'uses'        => 'HashesController@getOne'
    ]);
    $router->post('', [
        'as'          => 'hashes.create',
        'schema'      => '/v1/hashes/create.json',
        'description' => 'Create new hash',
        'uses'        => 'HashesController@create'
    ]);
    $router->put('/{hash_rev:[0-9a-z]+}', [
        'as'          => 'hashes.update',
        'description' => 'Update hash',
        'schema'      => '/v1/hashes/update.json',
        'uses'        => 'HashesController@update'
    ]);
    $router->delete('/{hash_rev:[0-9a-z]+}', [
        'as'          => 'hash.delete',
        'description' => 'Delete hash',
        'uses'        => 'HashesController@delete'
    ]);
});
