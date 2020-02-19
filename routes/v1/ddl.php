<?php

$router->group([
    'prefix' => 'ddl',
    'namespace' => '\Modules\Ddl\Http\Controllers'
], function () use ($router) {
    $router->post('', [
        'as'          => 'ddl.create',
        'description' => 'Commit ddl',
        'uses'        => 'DdlController@commit'
    ]);
});
