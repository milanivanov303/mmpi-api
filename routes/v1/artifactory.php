<?php

$router->group([
    'prefix' => 'artifactory',
    'namespace' => '\Modules\Artifactory\Http\Controllers'
], function () use ($router) {
    $router->get('{uri:.+}', [
        'as'          => 'artifactory.list',
        'description' => 'Get result list',
        'uses'        => 'ArtifactoryController@execute'
    ]);
});
