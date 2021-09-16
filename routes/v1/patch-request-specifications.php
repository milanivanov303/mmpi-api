<?php

$router->group([
    'prefix' => 'patch-request-specifications',
    'namespace' => '\Modules\PatchRequestSpecifications\Http\Controllers'
], function () use ($router) {
    $router->put('/patch-request/{patch_request_id}/user/{user_id}', [
        'as'          => 'patch-request-specifications.one',
        //'schema'      => '/v1/project-specifics/project-specific.json',
        'description' => 'Store single patch request specification',
        'uses'        => 'PatchRequestSpecificationsController@storePatchRequestSpecifications'
    ]);
    $router->delete('/patch-request/{patch_request_id}/user/{user_id}', [
        'as'          => 'patch-request-specifications.delete',
        'description' => 'Delete patch request specification',
        'uses'        => 'PatchRequestSpecificationsController@deletePatchRequestSpecifications'
    ]);
});
