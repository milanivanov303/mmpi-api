<?php

$router->group([
    'prefix' => 'certificates',
    'namespace' => '\Modules\Certificates\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'certificates.list',
        'schema'      => '/v1/certificates/instance.json',
        'description' => 'Get certificates list',
        'uses'        => 'CertificatesController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'certificates.one',
        'schema'      => '/v1/certificates/instance.json',
        'description' => 'Get single certificate',
        'uses'        => 'CertificatesController@getOne'
    ]);
    $router->post('', [
        'as'          => 'certificates.create',
        'schema'      => '/v1/certificates/create.json',
        'description' => 'Create new certificate',
        'uses'        => 'CertificatesController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'certificates.update',
        'description' => 'Update certificate',
        'schema'      => '/v1/certificates/update.json',
        'uses'        => 'CertificatesController@update'
    ]);
    $router->delete('/{id}', [
        'as'          => 'certificates.delete',
        'description' => 'Delete certificate',
        'uses'        => 'CertificatesController@delete'
    ]);
});
