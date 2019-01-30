<?php

namespace Modules\JsonRPC\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;
use JsonRPC\Server;
use Modules\JsonRPC\Procedures\HeadMergeRequest;

class JsonRpcController extends BaseController
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * JsonRpcController constructor.
     */
    public function __construct()
    {
        $this->server = new Server();
        $this->defineProcedures();
    }

    /**
     * Execute request
     *
     * @param Response $response
     * @return Response
     */
    public function execute(Response $response)
    {
        try {
            $jsonRpcResponse = $this->server->execute();
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

    /**
     * Define available procedures
     */
    protected function defineProcedures()
    {
        $this->server->getProcedureHandler()
            ->withClassAndMethod('head_merge_request', new HeadMergeRequest, 'process');
    }
}
