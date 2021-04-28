<?php

namespace Modules\Issues\Helpers\JsonSchema\Filters;

use Opis\JsonSchema\IFilter;
use Modules\Issues\Models\Issue;
use Illuminate\Support\Facades\DB;

class CheckModificationsFilter implements IFilter
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
        $issue         = $this->getIssue();
        $parentIssueId = app(Issue::class)->getModelId((array) $data, 'tts_id');

        // check if there are modifications for this issue attached to patch request
        if ($issue && $issue->parent_issue_id !== $parentIssueId) {
            if ($this->hasModificationsAddedInPatchRequest($issue->id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get Issue
     *
     * @return Issue|null
     */
    protected function getIssue() : ?Issue
    {
        $ttsId = app('request')->route()[2]['tts_id'];
        return Issue::where('tts_id', $ttsId)->first();
    }

    /**
     * Has modifications added to patch request
     *
     * @param int $issueId
     * @return bool
     */
    protected function hasModificationsAddedInPatchRequest(int $issueId) : bool
    {
        $count = DB::select(
            'select count(*)
                    from issues i
                    join modifications m on i.id = m.issue_id
                    join modif_to_pr mpr on m.id = mpr.modif_id
                    join patch_requests pr on mpr.pr_id = pr.id
                where i.id = ?',
            [
                $issueId
            ]
        );

        return $count > 0;
    }
}
