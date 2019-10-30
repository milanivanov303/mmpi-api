<?php

namespace Modules\Departments\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Departments\Repositories\DepartmentRepository;

class DepartmentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DepartmentRepository $repository
     * @return void
     */
    public function __construct(DepartmentRepository $repository)
    {
        $this->repository = $repository;
    }
}
