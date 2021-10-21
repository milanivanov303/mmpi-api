<?php

namespace Modules\Issues\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Issues\Helpers\JsonSchema\Filters\CheckParentFilter;
use Modules\Issues\Helpers\JsonSchema\Filters\CheckModificationsFilter;

class IssuesServiceProvider extends ServiceProvider
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

        $filters->add("object", "checkIssueParent", new CheckParentFilter);
        $filters->add("null", "checkIssueParent", new CheckParentFilter);

        $filters->add("object", "checkIssueModifications", new CheckModificationsFilter);
        $filters->add("null", "checkIssueModifications", new CheckModificationsFilter);

        $validator->setFilters($filters);
    }
}
