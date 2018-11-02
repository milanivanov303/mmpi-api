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
        require 'api/v1/users.php';

        // Hashes
        require 'api/v1/hashes.php';

        // Issues
        require 'api/v1/issues.php';

        // Projects
        require 'api/v1/projects.php';

        // Instances
        require 'api/v1/instances.php';

        // Delivery chains
        require 'api/v1/delivery-chains.php';

        // Modifications
        require 'api/v1/modifications.php';

        // Patch Requests
        require 'api/v1/patch-requests.php';
    });
});
