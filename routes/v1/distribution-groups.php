<?php

$router->group([
    'prefix'     => 'distribution_groups',
    'namespace'  => '\Modules\DistributionGroups\Http\Controllers',
], function () use ($router) {
    $router->get('', [
        'as'          => 'distribution_groups.list',
        'schema'      => '/v1/distribution-group.json',
        'description' => 'Get all distribution groups',
        'uses'        => 'DistributionGroupController@getMany'
    ]);
    $router->get('/{samaccountname}', [
        'as'          => 'distribution_group.one',
        'schema'      => '/v1/distribution-group.json',
        'description' => 'Get single distribution group',
        'uses'        => 'DistributionGroupController@getOne'
    ]);
});
