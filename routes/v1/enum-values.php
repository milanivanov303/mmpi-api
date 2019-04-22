<?php

$router->group([
    'prefix' => 'enum-values',
    'namespace' => '\Modules\EnumValues\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'enum-values.list',
        'schema'      => '/v1/enum-value.json',
        'description' => 'Get enum values',
        'uses'        => 'EnumValuesController@getMany'
    ]);
    $router->get('/{key}/{type}', [
        'as'          => 'enum-values.one',
        'schema'      => '/v1/enum-value.json',
        'description' => 'Get enum values',
        'uses'        => 'EnumValuesController@getOne'
    ]);
});
