<?php

$router->group([
    'prefix' => 'jsonrpc',
    'namespace' => '\Modules\JsonRpc\Http\Controllers'
], function () use ($router) {
    $router->post('', [
        'as'          => 'jsonrpc',
        'schema'      => '/v1/jsonrpc/procedures.json',
        'description' => 'JSON RPC',
        'uses'        => 'JsonRpcController@execute',
        'openapi'     => [
            'responses' => [
                '200' => [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'jsonrpc' => [
                                        '$ref' => '#/components/schemas/JsonRpc/properties/jsonrpc'
                                    ],
                                    'result' => [
                                        'type' => 'string',
                                        'description' => 'Procedure call result'
                                    ],
                                    'id' => [
                                        '$ref' => '#/components/schemas/JsonRpc/properties/id'
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
                '400' => [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'jsonrpc' => [
                                        '$ref' => '#/components/schemas/JsonRpc/properties/jsonrpc'
                                    ],
                                    'error' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'code' => [
                                                'type' => 'number',
                                                'description' => 'Error code'
                                            ],
                                            'message' => [
                                                'type' => 'string',
                                                'description' => 'Error message'
                                            ]
                                        ]
                                    ],
                                    'id' => [
                                        '$ref' => '#/components/schemas/JsonRpc/properties/id'
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => ['$ref' => '#/components/responses/Unauthorized'],
                '422' => ['$ref' => '#/components/responses/ValidationError']
            ]
        ]
    ]);
});
