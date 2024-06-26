<?php

$router->group([
    'prefix' => 'enum-values',
    'namespace' => '\Modules\EnumValues\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'enum-values.list',
        'schema'      => '/v1/enum-values/enum-value.json',
        'description' => 'Get enum values',
        'uses'        => 'EnumValuesController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'enum-values.one',
        'schema'      => '/v1/enum-values/enum-value.json',
        'description' => 'Get enum values',
        'uses'        => 'EnumValuesController@getOne'
    ]);
    $router->get('/{type}/{key}', [
        'as'          => 'enum-values.one.type_and_key',
        'schema'      => '/v1/enum-values/enum-value.json',
        'description' => 'Get enum values',
        'uses'        => 'EnumValuesController@getOne'
    ]);
    $router->post('', [
        'as'          => 'enum-values.create',
        'schema'      => '/v1/enum-values/create.json',
        'description' => 'Create new enum value',
        'uses'        => 'EnumValuesController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'enum-values.update',
        'description' => 'Update enum values',
        'schema'      => '/v1/enum-values/update.json',
        'uses'        => 'EnumValuesController@update'
    ]);
    $router->put('/{type}/{key}', [
        'as'          => 'enum-values.update.type_and_key',
        'description' => 'Update enum values',
        'schema'      => '/v1/enum-values/update.json',
        'uses'        => 'EnumValuesController@update'
    ]);
    $router->delete('/{id}', [
        'as'          => 'enum-values.delete',
        'description' => 'Delete project',
        'uses'        => 'EnumValuesController@delete'
    ]);
    $router->delete('/{type}/{key}', [
        'as'          => 'enum-values.delete.type_and_key',
        'description' => 'Delete project',
        'uses'        => 'EnumValuesController@delete'
    ]);
});
