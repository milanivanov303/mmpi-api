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

$router->get('/', function () {
    return view('welcome');
});

$router->group([
    'namespace' => '\Rap2hpoutre\LaravelLogViewer'
], function () use ($router) {
    $router->get('logs.html', 'LogViewerController@index');
});

// v1
$router->group(['prefix' => 'v1'], function () use ($router) {

    $router->group(['middleware' => ['audit', 'auth', 'json-validator']], function () use ($router) {

        // Users
        require 'v1/users.php';

        // Hashes
        require 'v1/hashes.php';

        // Issues
        require 'v1/issues.php';

        // Enum values
        require 'v1/enum-values.php';

        // Projects
        require 'v1/projects.php';

        // Project events
        require 'v1/project-events.php';

        // Project event estimations
        require 'v1/project-event-estimations.php';

        // Instances
        require 'v1/instances.php';

        // Installations
        require 'v1/installations.php';

        // Delivery chains
        require 'v1/delivery-chains.php';

        // Modifications
        require 'v1/modifications.php';

        // Patch Requests
        require 'v1/patch-requests.php';

        // Patches
        require 'v1/patches.php';

        // IMX certificates
        require 'v1/certificates.php';

        // Json RPC. All nasty stuff goes here
        require 'v1/jsonrpc.php';

        // OCI. All nasty stuff goes here
        require 'v1/oci.php';

        // Instance downtimes
        require 'v1/instance-downtimes.php';

        // Hash branches
        require 'v1/branches.php';

        // Project specifics
        require 'v1/project-specifics.php';

        // Source
        require 'v1/sources.php';

        // Source dependencies
        require 'v1/source-dependencies.php';

        // Source revisions
        require 'v1/source-revisions.php';

        // Artifactory
        require 'v1/artifactory.php';
        
        // Departments
        require 'v1/departments.php';

        // Auth
        require 'v1/auth.php';

        // Ddl
        require 'v1/ddl.php';
    });
});
