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
    $router->post('', [
        'as'          => 'departments.create',
        'schema'      => '/v1/departments/create.json',
        'description' => 'Create new department',
        'uses'        => 'DepartmentsController@create'
    ]);
    $router->put('/{id}', [
        'as'          => 'departments.update',
        'description' => 'Update departments',
        'schema'      => '/v1/departments/update.json',
        'uses'        => 'DepartmentsController@update'
    ]);
    $router->delete('/{id}', [
        'as'          => 'departments.delete',
        'description' => 'Delete departments',
        'uses'        => 'DepartmentsController@delete'
    ]);
});
