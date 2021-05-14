<?php

namespace Modules\Issues\Helpers\JsonSchema\Filters;

use Opis\JsonSchema\IFilter;
use Modules\Issues\Models\Issue;

class CheckParentFilter implements IFilter
{
    /**
     * Validate
     *
     * @param mixed $data
     * @param array $args
     * @return bool
     */
    public function validate($data, array $args): bool
    {
        $issueId = app(Issue::class)->getModelId((array) $data, 'tts_id');
        $issue   = app(Issue::class)->find($issueId);

        // check if parent issue has parent We do not allow multi level nesting
        if ($issue && $issue->parent_issue_id) {
            return false;
        }

        return true;
    }
}
