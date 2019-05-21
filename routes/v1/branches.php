<?php

$router->group([
    'prefix' => 'branches',
    'namespace' => '\Modules\Branches\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'branches.list',
        'schema'      => '/v1/branches/branch.json',
        'description' => 'Get branches list',
        'uses'        => 'BranchesController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'branches.one',
        'schema'      => '/v1/branches/branch.json',
        'description' => 'Get single branch',
        'uses'        => 'BranchesController@getOne'
    ]);
    $router->post('', [
        'as'          => 'branches.create',
        'schema'      => '/v1/branches/create.json',
        'description' => 'Create new branch',
        'uses'        => 'BranchesController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'branches.update',
        'description' => 'Update branches',
        'schema'      => '/v1/branches/update.json',
        'uses'        => 'BranchesController@update'
    ]);
    $router->delete('/{id}', [
        'as'          => 'branches.delete',
        'description' => 'Delete branches',
        'uses'        => 'BranchesController@delete'
    ]);
});
