<?php

$router->group([
    'prefix' => 'oci',
    'namespace' => '\Modules\Oci\Http\Controllers'
], function () use ($router) {
    $router->post('', [
        'as'          => 'oci',
        'schema'      => '/v1/oci/oci.json',
        'description' => 'OCI connection',
        'uses'        => 'OciController@execute',
        'tags'        => [],
        'openapi'     => [
            'schema' => '/v1/oci/response.json',
        ]
    ]);
});
