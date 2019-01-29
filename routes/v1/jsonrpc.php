<?php

$router->group([
    'prefix' => 'jsonrpc',
    'namespace' => '\Modules\JsonRPC\Http\Controllers'
], function () use ($router) {
    $router->post('', [
        'as'          => 'jsonrpc',
        'schema'      => '/v1/jsonrpc.json',
        'description' => 'JSON RPC',
        'uses'        => 'JsonRpcController@execute'
    ]);
});
