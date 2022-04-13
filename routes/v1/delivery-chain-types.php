<?php

$router->group([
    'prefix' => 'delivery-chain-types',
    'namespace' => '\Modules\DeliveryChainTypes\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'delivery-chain-types.list',
        'description' => 'Get delivery chain types',
        'uses'        => 'DeliveryChainTypesController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'delivery-chain-types.one',
        'description' => 'Get specific delivery chain type',
        'uses'        => 'DeliveryChainTypesController@getOne'
    ]);
});
