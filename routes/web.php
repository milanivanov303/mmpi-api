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
            $router->post('', ['as' => 'api/v1/users/create', 'uses' => 'UsersController@create']);
            $router->put('/{id:[0-9]+}', ['as' => 'api/v1/users/update', 'uses' => 'UsersController@update']);
            $router->delete('/{id:[0-9]+}', 'UsersController@delete');
        });
        
        // Hashes
        $router->group(['prefix' => 'hashes'], function () use ($router) {
            $router->get('', 'HashesController@many');
            $router->get('/{id:[0-9]+}', 'HashesController@show');
            $router->post('', ['as' => 'api/v1/hashes/create', 'uses' => 'HashesController@create']);
            $router->put('/{id:[0-9]+}', ['as' => 'api/v1/hashes/update', 'uses' => 'HashesController@update']);
            $router->delete('/{id:[0-9]+}', 'HashesController@delete');
        });
        
        // Dependencies
        $router->group(['prefix' => 'dependencies'], function () use ($router) {
            $router->get('', 'DependenciesController@many');
            $router->get('/{id:[0-9]+}', 'DependenciesController@show');
            $router->post('', ['as' => 'api/v1/dependencies/create', 'uses' => 'DependenciesController@create']);
            $router->put('/{id:[0-9]+}', ['as' => 'api/v1/dependencies/update', 'uses' => 'DependenciesController@update']);
            $router->delete('/{id:[0-9]+}', 'DependenciesController@delete');
        });
        
    });
});
