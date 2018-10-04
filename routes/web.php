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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


// API
$router->group([
    'prefix'     => 'api',
    'middleware' => ['auth', 'json-validator']
], function () use ($router) {

    // v1
    $router->group(['prefix' => 'v1'], function () use ($router) {

        // Users
        $router->group(['prefix' => 'users'], function () use ($router) {
           $router->get('', 'UsersController@many');
            $router->get('/{id:[0-9]+}', 'UsersController@show');
            $router->post('', [
                'as' => 'users.create',
                'description' => 'Create new user',
                'schema' => '/api/v1/users/create.json',
                'uses' => 'UsersController@create'
            ]);
            $router->put('/{id:[0-9]+}', [
                'uses' => 'UsersController@update']
            );
            $router->delete('/{id:[0-9]+}', 'UsersController@delete');
        });
        
        // Hashes
        $router->group(['prefix' => 'hashes'], function () use ($router) {
            $router->get('', 'HashesController@getMany');
            $router->get('/{hash_rev:[0-9a-z]+}', 'HashesController@getOne');
            $router->post('', [
                'schema' => '/api/v1/hashes/create.json',
                'description' => 'Register new hash',
                'uses'   => 'HashesController@create']
            );
            $router->put('/{hash_rev:[0-9a-z]+}', [
                'schema' => '/api/v1/hashes/update.json',
                'uses' => 'HashesController@update']
            );
            $router->delete('/{hash_rev:[0-9a-z]+}', 'HashesController@delete');
        });
        
        // Dependencies
        //$router->group(['prefix' => 'dependencies'], function () use ($router) {
        //    $router->get('', 'DependenciesController@many');
        //    $router->get('/{id:[0-9]+}', 'DependenciesController@show');
        //    $router->post('', ['as' => 'api/v1/dependencies/create', 'uses' => 'DependenciesController@create']);
        //    $router->put('/{id:[0-9]+}', ['as' => 'api/v1/dependencies/update', 'uses' => 'DependenciesController@update']);
        //    $router->delete('/{id:[0-9]+}', 'DependenciesController@delete');
        //});
        
    });
});
