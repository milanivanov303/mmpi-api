<?php

namespace Modules\JsonRpc\Providers;

use Illuminate\Support\ServiceProvider;
use JsonRPC\Server;
use Modules\JsonRpc\Procedures\HeadMergeRequest;
use Modules\JsonRpc\Procedures\Cppcheck;
use Modules\JsonRpc\Procedures\InsertHash;

class JsonRpcServiceProvider extends ServiceProvider
{
    /**
     * Register issues services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('JsonRpcServer', function () {
            $server = new Server();

            $server->getProcedureHandler()
                ->withClassAndMethod('head_merge_request', new HeadMergeRequest, 'process')
                ->withClassAndMethod('run_cppcheck', new Cppcheck, 'run')
                ->withClassAndMethod('insert_hash', new InsertHash, 'insert');

            return $server;
        });
    }
}
