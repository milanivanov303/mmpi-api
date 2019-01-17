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

// v1
$router->group(['prefix' => 'v1'], function () use ($router) {

    // Auth
    require 'v1/auth.php';

    $router->group(['middleware' => ['auth', 'json-validator', 'audit']], function () use ($router) {

        // Users
        require 'v1/users.php';

        // Hashes
        require 'v1/hashes.php';

        // Issues
        require 'v1/issues.php';

        // Projects
        require 'v1/projects.php';

        // Instances
        require 'v1/instances.php';

        // Delivery chains
        require 'v1/delivery-chains.php';

        // Modifications
        require 'v1/modifications.php';

        // Patch Requests
        require 'v1/patch-requests.php';

        // Patches
        require 'v1/patches.php';
    });
});
