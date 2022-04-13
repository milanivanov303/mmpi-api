<?php

$router->group([
    'prefix' => 'sources',
    'namespace' => '\Modules\Sources\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'sources.list',
        'schema'      => '/v1/sources/source.json',
        'description' => 'Get source list',
        'uses'        => 'SourcesController@getMany'
    ]);
    $router->post('/search', [
        'as'          => 'sources.search',
        'description' => 'Search sorces by file',
        'uses'        => 'SourcesController@search'
    ]);
    $router->get('/{id}', [
        'as'          => 'sources.one',
        'schema'      => '/v1/sources/source.json',
        'description' => 'Get single source',
        'uses'        => 'SourcesController@getOne'
    ]);
    // $router->post('', [
    //     'as'          => 'sources.create',
    //     'schema'      => '/v1/sources/create.json',
    //     'description' => 'Create new source',
    //     'uses'        => 'SourcesController@create'
    // ]);
    // $router->put('/{name}', [
    //     'as'          => 'sources.update',
    //     'description' => 'Update source',
    //     'schema'      => '/v1/sources/update.json',
    //     'uses'        => 'SourcesController@update'
    // ]);
    // $router->delete('/{name}', [
    //     'as'          => 'sources.delete',
    //     'description' => 'Delete source',
    //     'uses'        => 'SourcesController@delete'
    // ]);
});
