<?php

namespace Modules\JsonRpc\Procedures;

use Illuminate\Support\Facades\DB;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use Modules\Modifications\Models\Modification;

class HeadMergeRequest
{
    /**
     * Process procedure
     *
     * @param string $ttsId
     * @return mixed
     */
    public function process(string $ttsId)
    {
        $sources = $this->getSources($ttsId);

        $output = [];
        foreach ($sources as $source) {
            try {
                $issue = $this->createIssue($source);

                $modification = Modification::find($source->modif_id);
                $modification->requested_head_merge = 1;
                $modification->saveOrFail();

                $output[] = [
                    'modification_id' => $source->modif_id,
                    'issue'           => $issue
                ];
            } catch (\Exception $e) {
                $output[] = [
                    'modification_id' => $source->modif_id,
                    'error'           => $e->getMessage()
                ];
            }
        }

        return $output;
    }

    /**
     * Get sources
     *
     * @param string $ttsId
     * @return array
     */
    protected function getSources(string $ttsId) : array
    {
        return DB::select(
            "
            SELECT I.id,
               I.tts_id,
               M.id AS modif_id,
               M.name AS source_file,
               U.username,
               S.source_id,
               SR.rev_id,
               SR.revision,
               CM.commit_id,
               CM.merge_commit,
               BSR.rev_id AS backport_rev_id,
               BSR.revision AS backport_revision,
               MSR.rev_id AS merged_in_rev_id,
               MSR.revision AS merged_in_revision
            FROM issues I
            JOIN modifications M ON I.id=M.issue_id
            JOIN users U ON IFNULL(M.updated_by_id, M.created_by_id)=U.id
            JOIN source S ON M.name=CONCAT(S.source_path, '/', S.source_name)
            JOIN source_revision SR ON (S.source_id=SR.source_id AND M.version=SR.revision)
            JOIN enum_values EVS ON (EVS.type='revision_log_type' AND EVS.`key`='cvs')
            JOIN enum_values EVT ON (EVT.type='cvs_log_tags_stack' AND EVT.`key`='cvs_tag_merge')
            LEFT JOIN commit_merge CM ON 
              ((SR.rev_id=CM.commit_id OR SR.rev_id=CM.merge_commit) AND CM.commit_log_type_id=EVS.id)
            LEFT JOIN source_revision BSR ON 
              (CM.merge_commit<>SR.rev_id AND CM.merge_commit=BSR.rev_id AND BSR.revision NOT LIKE '%.%.%')
            LEFT JOIN source_revision MSR ON (CM.commit_id=MSR.rev_id AND MSR.revision NOT LIKE '%.%.%')
            WHERE 
                I.tts_id=?
                AND M.type_id='source'
                AND (M.requested_head_merge IS NULL OR M.requested_head_merge<>1)
                AND M.version LIKE '%.%.%'
                AND MSR.rev_id IS NULL
                AND BSR.rev_id IS NULL
            ",
            [$ttsId]
        );
    }

    /**
     * Get issue data
     *
     * @param \stdClass $source
     * @return IssueField
     */
    protected function getIssue(\stdClass $source)
    {
        $issueField = new IssueField();

        $issueField
            ->setProjectKey("FIRS")//CVS_Head_Merge
            ->setSummary("Commit on Head the changes done in {$source->tts_id}")
            ->setAssigneeName($source->username)
            ->setIssueType("Internal") //Short Task
            //->setPriorityName('Major')
            ->setDescription("
                The test of task '{$source->tts_id}' is completed OK. Please commit your changes in the HEAD revision.
                
                *Source:* {$source->source_file}
                *Revision:* {$source->revision}
                *Merge Commit:* {$source->merge_commit}
                *Backport Revision:* {$source->backport_revision}
                *Merged In Revision:* {$source->merged_in_revision}
            ");

        return $issueField;
    }

    /**
     * Create issues - batch
     *
     * @param \stdClass $source
     * @return mixed
     *
     * @throws \Exception
     */
    protected function createIssue(\stdClass $source)
    {
        $issue = $this->getIssue($source);

        $issueService = new IssueService();
        return $issueService->create($issue);
    }
}
