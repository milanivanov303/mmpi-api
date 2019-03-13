<?php

$router->group([
    'prefix' => 'delivery-chains',
    'namespace' => '\Modules\DeliveryChains\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'delivery-chains.list',
        'schema'      => '/v1/delivery-chains/delivery-chain.json',
        'description' => 'Get delivery chains list',
        'uses'        => 'DeliveryChainsController@getMany'
    ]);
    $router->get('/{title}', [
        'as'          => 'delivery-chains.one',
        'schema'      => '/v1/delivery-chains/delivery-chain.json',
        'description' => 'Get single delivery chain',
        'uses'        => 'DeliveryChainsController@getOne'
    ]);
    $router->post('', [
        'as'          => 'delivery-chains.create',
        'schema'      => '/v1/delivery-chains/create.json',
        'description' => 'Create new delivery chain',
        'uses'        => 'DeliveryChainsController@create'
    ]);
    $router->put('/{title}', [
        'as'          => 'delivery-chains.update',
        'description' => 'Update delivery chain',
        'schema'      => '/v1/delivery-chains/update.json',
        'uses'        => 'DeliveryChainsController@update'
    ]);
    $router->delete('/{title}', [
        'as'          => 'delivery-chains.delete',
        'description' => 'Delete delivery chain',
        'uses'        => 'DeliveryChainsController@delete'
    ]);
});
