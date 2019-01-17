<?php

$router->group([
    'prefix' => 'auth',
    'namespace' => '\Core\Http\Controllers'
], function () use ($router) {

    $router->post('/login', [
        'as'          => 'auth.login',
        'schema'      => '/v1/auth/login.json',
        'description' => 'Authenticate and get JWT',
        'uses'        => 'AuthController@login',
        'tags'        => ['auth'],
        'openapi'     => [
            'responses' => [
                '200' => [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'token' => [
                                        'type' => 'string',
                                        'description' => 'Use this token to make requests to the api'
                                    ],
                                    'refresh_token' => [
                                        'type' => 'string',
                                        'description' => 'Has longer live and can be used to get new 
                                                          token without sending user and password over the network'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    '$ref' => '#/components/responses/Unauthorized'
                ]
            ],
            'security' => []
        ]
    ]);

    $router->post('/refresh', [
        'as'          => 'auth.refresh',
        'schema'      => '/v1/auth/refresh.json',
        'description' => 'Refresh JWT',
        'uses'        => 'AuthController@refresh',
        'tags'        => ['auth'],
        'openapi'     => [
            'responses' => [
                '200' => [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'token' => [
                                        'type' => 'string',
                                        'description' => 'Use this token to make requests to the api'
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
                '401' => [
                    '$ref' => '#/components/responses/Unauthorized'
                ]
            ],
            'security' => []
        ]
    ]);
});
