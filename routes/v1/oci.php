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
            'responses' => [
                '200' => [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'query' => [
                                        '$ref' => '#/components/schemas/Oci/properties/query'
                                    ],
                                    'instance' => [
                                        '$ref' => '#/components/schemas/Oci/properties/instance'
                                    ],
                                    'result' => [
                                        'type' => 'string',
                                        'description' => 'Oci call result'
                                    ]
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
                                    'oci' => [
                                        '$ref' => '#/components/schemas/Oci/properties/query'
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
                                    ]
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
