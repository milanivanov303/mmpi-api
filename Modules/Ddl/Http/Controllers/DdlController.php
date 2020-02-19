<?php

namespace Modules\Ddl\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ddl\Services\DdlService;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Resources\Json\JsonResource;

class DdlController extends BaseController
{
    /**
     * Commit Ddl
     *
     * @param Request $request
     * @return JsonResource
     */
    public function commit(Request $request)
    {
        try {
            $parameters = $request->all();

            $request = new DdlService($parameters['content'], $parameters['file_name'], $parameters['branch']);

            $request->run();

            return  response()->json(['success'=>'You have successfully commit file.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }
}
