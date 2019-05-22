<?php

namespace Modules\Hashes\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Hashes\Helpers\JsonSchema\Filters\CheckHashBranchFilter;

class HashesServiceProvider extends ServiceProvider
{
    /**
     * Register issues services.
     *
     * @return void
     */
    public function register()
    {
        $validator = app('OpisJsonValidator');

        $filters = $validator->getFilters();

        $filters->add("object", "checkHashBranch", new CheckHashBranchFilter);

        $validator->setFilters($filters);
    }
}
