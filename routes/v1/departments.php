<?php

$router->group([
    'prefix' => 'departments',
    'namespace' => '\Modules\Departments\Http\Controllers'
], function () use ($router) {
    $router->get('', [
        'as'          => 'departments.list',
        'schema'      => '/v1/departments/department.json',
        'description' => 'Get departments list',
        'uses'        => 'DepartmentsController@getMany'
    ]);
    $router->get('/{id}', [
        'as'          => 'departments.one',
        'schema'      => '/v1/departments/department.json',
        'description' => 'Get single department',
        'uses'        => 'DepartmentsController@getOne'
    ]);
});
