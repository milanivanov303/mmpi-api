<?php

/*
\Illuminate\Support\Facades\Event::listen(
    \Illuminate\Database\Events\QueryExecuted::class,
    function ($query) {
        var_dump($query->sql);
    }
);
*/

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
        require_once 'api/v1/users.php';
        
        // Hashes
        require_once 'api/v1/hashes.php';

        // Issues
        require_once 'api/v1/issues.php';
    });
});
