<?php

namespace Modules\EnumValues\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\EnumValues\Repositories\EnumValueRepository;

class EnumValuesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EnumValueRepository $repository
     * @return void
     */
    public function __construct(EnumValueRepository $repository)
    {
        $this->repository = $repository;
    }
}
