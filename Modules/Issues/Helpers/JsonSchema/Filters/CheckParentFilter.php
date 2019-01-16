<?php

namespace Modules\Issues\Helpers\JsonSchema\Filters;

use Opis\JsonSchema\IFilter;
use Modules\Issues\Models\Issue;

class CheckParentFilter implements IFilter
{
    /**
     * Validate
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    public function validate($value, array $args): bool
    {
        // skip validation for null values
        if (is_null($value)) {
            return true;
        }

        $parent = app(Issue::class)->where('tts_id', $value->tts_id)->first();

        // check if parent issue has parent We do not allow multi level nesting
        if ($parent && $parent->parent_issue_id) {
            return false;
        }

        return true;
    }
}
