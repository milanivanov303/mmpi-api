<?php

$router->group([
    'prefix' => 'installations',
    'namespace' => '\Modules\Installations\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'installations.list',
        'schema'      => '/v1/installations/installation.json',
        'description' => 'Get installations list',
        'uses'        => 'InstallationsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'installations.one',
        'schema'      => '/v1/installations/installation.json',
        'description' => 'Get single installation',
        'uses'        => 'InstallationsController@getOne'
    ]);
    // $router->post('', [
    //     'as'          => 'installations.create',
    //     'schema'      => '/v1/installations/create.json',
    //     'description' => 'Create new instance',
    //     'uses'        => 'InstallationsController@create'
    // ]);
    // $router->put('/{id}', [
    //     'as'          => 'installations.update',
    //     'description' => 'Update installation',
    //     'schema'      => '/v1/installations/update.json',
    //     'uses'        => 'InstallationsController@update'
    // ]);
    // $router->delete('/{id}', [
    //     'as'          => 'installations.delete',
    //     'description' => 'Delete installation',
    //     'uses'        => 'InstallationsController@delete'
    // ]);
});
