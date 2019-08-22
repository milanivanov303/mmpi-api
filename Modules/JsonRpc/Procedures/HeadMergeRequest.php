<?php

namespace Modules\JsonRpc\Procedures;

use Modules\SourceRevisions\Models\SourceRevision;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;

class HeadMergeRequest
{
    /**
     * tts_id of issue that was closed
     *
     * @var string
     */
    protected $ttsId;

    /**
     * Project key to create head merge issues in
     *
     * @var string
     */
    protected $devProjectKey;

    /**
     * Process procedure
     *
     * @param string $ttsId
     * @param string $devProjectKey
     * @return mixed
     */
    public function process(string $ttsId, string $devProjectKey)
    {
        $this->ttsId         = $ttsId;
        $this->devProjectKey = $devProjectKey;

        $data = json_decode(json_encode($this->getData()), JSON_OBJECT_AS_ARRAY);

        $data = collect($data)->groupBy('username');

        $output = [];
        foreach ($data as $username => $sources) {
            $revisions = $sources->pluck('rev_id')->all();

            try {
                $issue = $this->createIssue($username, $sources);

                SourceRevision::whereIn('rev_id', $revisions)
                    ->update(['requested_head_merge' => 1]);

                $output[] = [
                    'revisions' => $revisions,
                    'issue'     => $issue
                ];
            } catch (\Exception $e) {
                Log::error($e->getMessage());

                $output[] = [
                    'revisions' => $revisions,
                    'error'     => $e->getMessage()
                ];
            }
        }

        return $output;
    }

    /**
     * Get data
     *
     * @return array
     */
    protected function getData() : array
    {
        return DB::select(
            "
            SELECT
               U.username,
               SR.rev_id,
               M.name AS source_file,
               SR.revision
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
                AND (SR.requested_head_merge IS NULL OR SR.requested_head_merge<>1)
                AND M.version LIKE '%.%.%'
                AND MSR.rev_id IS NULL
                AND BSR.rev_id IS NULL
            ",
            [$this->ttsId]
        );
    }

    /**
     * Get issue data
     *
     * @param string $username
     * @param Collection $sources
     *
     * @return IssueField
     */
    protected function getIssue(string $username, Collection $sources) : IssueField
    {
        $issueField = new IssueField();

        // Get sources list
        $sources = implode(
            PHP_EOL,
            $sources->map(function ($item) {
                return "{$item['source_file']} - {$item['revision']}";
            })->all()
        );

        $issueField
            ->setProjectKey($this->devProjectKey)
            ->setSummary("Commit on Head the changes done in {$this->ttsId}")
            ->setAssigneeName($username)
            ->setIssueType('Short Task')
            ->setPriorityName('Normal')
            ->setDescription("
                The test of task {$this->ttsId} is completed OK. Please commit your changes in the HEAD revision.
                
                *Sources:* 
                {$sources}
            ");

        // Set specification
        $issueField->addCustomField('customfield_10140', 'n/a');

        // Set sub-project
        $issueField->addCustomField('customfield_10123', 'n/a');

        // Set Milestone
        $issueField->addCustomField('customfield_10530', ['value' => 'Installation']);

        // Set DDCA
        try {
            $today = new \DateTime();
            $issueField->addCustomField(
                'customfield_10606',
                $today->modify('+5 day')->format('Y-m-d')
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $issueField;
    }

    /**
     * Create issues - batch
     *
     * @param string $username
     * @param Collection $sources
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function createIssue(string $username, Collection $sources)
    {
        $issue = $this->getIssue($username, $sources);

        $issueService = new IssueService();
        $newIssue = $issueService->create($issue);

        // Update reporter so we have initial reporter in history
        $issue->setReporterName($username);
        $issueService->update($newIssue->key, $issue);

        return $newIssue;
    }
}
