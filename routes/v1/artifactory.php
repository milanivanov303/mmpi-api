<?php

$router->group([
    'prefix' => 'artifactory',
    'namespace' => '\Modules\Artifactory\Http\Controllers'
], function () use ($router) {
    $router->get('{name:.+}', [
        'as'          => 'artifactory.list',
        'description' => 'Get result list',
        'uses'        => 'ArtifactoryController@execute'
    ]);
});
