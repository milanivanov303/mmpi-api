<?php

$router->group([
    'prefix' => 'gitlab',
    'namespace' => '\Modules\Gitlab\Http\Controllers'
], function () use ($router) {
    $router->get('/projects/{visibility}', [
        'description' => 'Get projects from gitlab server',
        'uses'        => 'GitlabController@projects'
    ]);
    $router->get('/project/show', [
        'description' => 'Show project',
        'uses'        => 'GitlabController@showProject'
    ]);
    $router->get('/project/branches', [
        'description' => 'Get branches of project',
        'uses'        => 'GitlabController@branches'
    ]);
    $router->get('/projects/branch/{name}', [
        'description' => 'Get branch of project',
        'uses'        => 'GitlabController@branch'
    ]);
    $router->get('/repotags', [
        'description' => 'Get repository tags',
        'uses'        => 'GitlabController@getRepoTags'
    ]);
    $router->get('/repo/commits', [
        'description' => 'Get commits from repository',
        'uses'        => 'GitlabController@commits'
    ]);
    $router->get('/repo/commitrefs/{sha}', [
        'description' => 'Get commitrefs',
        'uses'        => 'GitlabController@commitRefs'
    ]);
    $router->get('/namespaces', [
        'description' => 'Get namespaces',
        'uses'        => 'GitlabController@namespaces'
    ]);
    $router->get('/groups', [
        'description' => 'Get gitlab groups',
        'uses'        => 'GitlabController@groups'
    ]);
    $router->get('/group-projects', [
        'description' => 'Get gitlab groups',
        'uses'        => 'GitlabController@groupProjects'
    ]);
});
