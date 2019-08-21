<?php

$router->group([
    'prefix' => 'source-dependencies',
    'namespace' => '\Modules\SourceDependencies\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'source-dependencies.list',
        'schema'      => '/v1/source-dependencies/source-dependency.json',
        'description' => 'Get source dependency list',
        'uses'        => 'SourceDependenciesController@getMany'
    ]);
    $router->get('/{name}', [
        'as'          => 'source-dependencies.one',
        'schema'      => '/v1/source-dependencies/source-dependency.json',
        'description' => 'Get single source dependency',
        'uses'        => 'SourceDependenciesController@getOne'
    ]);
    $router->post('', [
        'as'          => 'source-dependencies.create',
        'schema'      => '/v1/source-dependencies/create.json',
        'description' => 'Create new source dependency',
        'uses'        => 'SourceDependenciesController@create'
    ]);
    $router->put('/{name}', [
        'as'          => 'source-dependencies.update',
        'description' => 'Update source dependency',
        'schema'      => '/v1/source-dependencies/update.json',
        'uses'        => 'SourceDependenciesController@update'
    ]);
    $router->delete('/{name}', [
        'as'          => 'source-dependencies.delete',
        'description' => 'Delete source dependency',
        'uses'        => 'SourceDependenciesController@delete'
    ]);
});
