<?php

namespace Modules\JsonRpc\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;

class JsonRpcController extends BaseController
{
    /**
     * Execute request
     *
     * @param Response $response
     * @return Response
     */
    public function execute(Response $response)
    {
        try {
            $jsonRpcResponse = app('JsonRpcServer')->execute();
            $jsonRpcResponse = json_decode($jsonRpcResponse, JSON_OBJECT_AS_ARRAY);

            if (array_key_exists('error', $jsonRpcResponse)) {
                $response = $response->setStatusCode(400);
            }

            return $response->setContent($jsonRpcResponse);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $response = $response->setContent($e->getMessage())->setStatusCode(400);
        }

        return $response;
    }
}
