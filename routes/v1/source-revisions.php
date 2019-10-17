<?php

$router->group([
    'prefix' => 'source-revisions',
    'namespace' => '\Modules\SourceRevisions\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'source-revisions.list',
        'schema'      => '/v1/source-revisions/source-revision.json',
        'description' => 'Get source revision list',
        'uses'        => 'SourceRevisionsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'source-revisions.one',
        'schema'      => '/v1/source-revisions/source-revision.json',
        'description' => 'Get single source revision',
        'uses'        => 'SourceRevisionsController@getOne'
    ]);
    // $router->post('', [
    //     'as'          => 'source-revisions.create',
    //     'schema'      => '/v1/source-revisions/create.json',
    //     'description' => 'Create new source revision',
    //     'uses'        => 'SourceRevisionsController@create'
    // ]);
    // $router->put('/{name}', [
    //     'as'          => 'source-revisions.update',
    //     'description' => 'Update source revision',
    //     'schema'      => '/v1/source-revisions/update.json',
    //     'uses'        => 'SourceRevisionsController@update'
    // ]);
    // $router->delete('/{name}', [
    //     'as'          => 'source-revisions.delete',
    //     'description' => 'Delete source revision',
    //     'uses'        => 'SourceRevisionsController@delete'
    // ]);
});
