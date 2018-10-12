<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// API
$router->group([
    'prefix'     => 'api',
    'middleware' => ['auth', 'json-validator']
], function () use ($router) {

    // v1
    $router->group(['prefix' => 'v1'], function () use ($router) {

        // Users
        $router->group(['prefix' => 'users'], function () use ($router) {
            $router->get('', [
                'as'          => 'users.list',
                'schema'      => '/api/v1/user.json',
                'description' => 'Get users list',
                'uses'        => 'UsersController@getMany'
            ]);
            $router->get('/{username}', [
                'as'          => 'users.one',
                'schema'      => '/api/v1/user.json',
                'description' => 'Get single user',
                'uses'        => 'UsersController@getOne'
            ]);
        });
        
        // Hashes
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
                'uses'        => 'HashesController@create']
            );
            $router->put('/{rev:[0-9a-z]+}', [
                'as'          => 'hashes.update',
                'description' => 'Update hash',
                'schema'      => '/api/v1/hash.json',
                'uses'        => 'HashesController@update']
            );
            $router->delete('/{rev:[0-9a-z]+}', [
                'as'          => 'hash.delete',
                'description' => 'Delete hash',
                'uses'        => 'HashesController@delete'
            ]);
        });
        
    });
});
