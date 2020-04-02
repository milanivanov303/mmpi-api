<?php

namespace Modules\Oci\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Oci\Procedures\OciRequest;
use Laravel\Lumen\Routing\Controller as BaseController;

class OciController extends BaseController
{
    /**
     * Execute request
     *
     * @param Request $request
     * @return Response
     */
    public function execute(Request $request)
    {
        $data = $request->all();
        try {
            $oci = app('OciConnect', $data['instance']);

            $query = new OciRequest($oci, $data['query']);

            $response = $query->run();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $response = $e->getMessage();
        }
        return $response;
    }
}
