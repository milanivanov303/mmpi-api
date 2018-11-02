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
});
