<?php

$router->group([
    'prefix' => 'auth',
    'namespace' => '\Core\Http\Controllers'
], function () use ($router) {

    $router->post('', [
        'as'          => 'auth',
        'schema'      => '/api/v1/auth.json',
        'description' => 'Authenticate and get JWT',
        'uses'        => 'AuthController@auth',
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
                                        'type' => 'string'
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

    $router->post('refresh', [
        'as'          => 'auth.refresh',
        'schema'      => '/api/v1/auth-refresh.json',
        'description' => 'Refresh JWT',
        'uses'        => 'AuthController@refresh'
    ]);
});
