<?php

$router->group([
    'prefix' => 'gitlab',
    'namespace' => '\Modules\Gitlab\Http\Controllers'
], function () use ($router) {
    $router->get('/projects/{visibility}', [
        'description' => 'Get projects from gitlab server',
        'uses'        => 'GitlabController@projects'
    ]);
    $router->get('/projects/show/{projectId}', [
        'description' => 'Show project',
        'uses'        => 'GitlabController@showProject'
    ]);
    $router->get('/projects/branches/{projectId}', [
        'description' => 'Show project',
        'uses'        => 'GitlabController@branches'
    ]);
    $router->get('/projects/{projectId}/branch/{name}', [
        'description' => 'Show project',
        'uses'        => 'GitlabController@branch'
    ]);
    $router->get('/repotags/{repoId}', [
        'description' => 'Get repository tags',
        'uses'        => 'GitlabController@getRepoTags'
    ]);
    $router->get('/repo/{projectId}/commits', [
        'description' => 'Get commits from repository',
        'uses'        => 'GitlabController@commits'
    ]);
    $router->get('/repo/{projectId}/commitrefs/{sha}', [
        'description' => 'Get commits from repository',
        'uses'        => 'GitlabController@commitRefs'
    ]);
});
