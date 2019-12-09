<?php

$router->group([
    'prefix' => 'auth',
    'namespace' => '\Modules\Auth\Http\Controllers'
], function () use ($router) {
    $router->get('user-permissions', [
        'as' => 'auth.user-permissions',
        'uses' => 'AuthController@getUserPermissions',
    ]);
});
