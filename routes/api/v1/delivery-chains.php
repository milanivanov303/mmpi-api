<?php

$router->group([
    'prefix' => 'delivery-chains',
    'namespace' => '\App\Modules\DeliveryChains\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'delivery-chains.list',
        'schema'      => '/api/v1/delivery-chains/delivery-chain.json',
        'description' => 'Get delivery chains list',
        'uses'        => 'DeliveryChainsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'delivery-chains.one',
        'schema'      => '/api/v1/delivery-chains/delivery-chain.json',
        'description' => 'Get single delivery chain',
        'uses'        => 'DeliveryChainsController@getOne'
    ]);
    $router->post('', [
        'as'          => 'delivery-chains.create',
        'schema'      => '/api/v1/delivery-chains/create.json',
        'description' => 'Create new delivery chain',
        'uses'        => 'DeliveryChainsController@create'
    ]);
    $router->put('/{rev:[0-9a-z]+}', [
        'as'          => 'delivery-chains.update',
        'description' => 'Update delivery chain',
        'schema'      => '/api/v1/delivery-chains/create.json',
        'uses'        => 'DeliveryChainsController@update'
    ]);
    $router->delete('/{rev:[0-9a-z]+}', [
        'as'          => 'delivery-chains.delete',
        'description' => 'Delete delivery chain',
        'uses'        => 'DeliveryChainsController@delete'
    ]);
});
