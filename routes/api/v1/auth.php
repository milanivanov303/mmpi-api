<?php
$router->post('auth', [
    'as'          => 'auth',
    'description' => 'Authenticate and get JWT',
    'uses'        => 'AuthController@auth'
]);
