<?php

namespace Modules\JsonRPC\Http\Controllers;

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
     */
    public function execute()
    {
        try {
            echo $this->server->execute();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
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
