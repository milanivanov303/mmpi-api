<?php
$router->post('auth', [
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
