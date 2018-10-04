<?php

namespace App\Console\Commands\Openapi;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

/**
 * Generate API documentation
 *
 * @category Console_Command
 */
class GenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "openapi:generate {version=v1}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generate API documentation";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $info = [
            "description" => "MMPI API",
            "version" => "1",
            "title" => "MMPI API"
        ];

        $servers = [
            [
                "url" => "http://yarnaudov.codixfr.private:8111/api/{$this->argument('version')}/"
            ]
        ];

        $document = new OADocument($info, $servers);

        foreach(Route::getRoutes() as $route) {
            $document->addPathItem(new OAPathItem($route));
        }

        echo $document->toJson();

        file_put_contents(storage_path('openapi.json'), $document->toJson());

        //echo json_encode($docs);

    }
}