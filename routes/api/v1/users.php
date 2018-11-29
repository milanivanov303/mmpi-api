<?php

$router->group([
    'prefix' => 'users',
    'namespace' => '\Modules\Users\Http\Controllers'
], function () use ($router) {
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
