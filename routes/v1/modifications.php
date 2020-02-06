<?php

use Illuminate\Support\Str;

$router->group([
    'prefix' => 'modifications',
    'namespace' => '\Modules\Modifications\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'modifications.list',
        'schema'      => '/v1/modifications/modification.json',
        'description' => 'Get modifications list',
        'uses'        => 'ModificationsController@getMany'
    ]);
    $router->get('/{id:[0-9]+}', [
        'as'          => 'modifications.one',
        'schema'      => '/v1/modifications/modification.json',
        'description' => 'Get single modification',
        'uses'        => 'ModificationsController@getOne'
    ]);
    $router->get('/{project_id}/{dlvry_type}', [
        'as'          => 'filtered.modifications.list',
        'description' => 'Get modifications by project and dlvry chain type',
        'uses'        => 'ModificationsController@getByProjectAndChainType'
    ]);

    $types = [
        'tables',
        'se-transfers',
        'sources',
        'temporary-sources',
        'binaries',
        'commands',
        'operations',
        'scm'
    ];

    foreach ($types as $prefix) {
        $router->group(['prefix' => $prefix], function () use ($router, $prefix) {
            $schema = Str::singular($prefix);
            $router->get('', [
                'as'          => "modifications.{$prefix}.list",
                'schema'      => "/v1/modifications/{$prefix}/{$schema}.json",
                'description' => "Get {$prefix} modifications list",
                'uses'        => 'ModificationsController@getMany',
                'type'        =>  $prefix,
                'tags'        => ['modifications']
            ]);
            $router->get('/{id:[0-9]+}', [
                'as'          => "modifications.{$prefix}.one",
                'schema'      => "/v1/modifications/{$prefix}/{$schema}.json",
                'description' => "Get single {$schema} modification",
                'uses'        => 'ModificationsController@getOne',
                'type'        => $prefix,
                'tags'        => ['modifications']
            ]);
            if ($prefix === 'binaries' || $prefix === 'commands' || $prefix === 'scm') {
                $router->post('', [
                    'as'          => "modifications.{$prefix}.create",
                    'schema'      => "/v1/modifications/{$prefix}/create.json",
                    'description' => "Create new {$prefix}",
                    'uses'        => 'ModificationsController@create',
                    'type'        =>  $prefix,
                    'tags'        => ['modifications']
                ]);
            }
        });
    }
});
