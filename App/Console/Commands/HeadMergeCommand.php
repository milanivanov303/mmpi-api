<?php

namespace App\Console\Commands;

use App\Mail\MissingProjectDevKeyMail;
use App\Models\PatchesHeadMerge;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use Modules\SourceRevisions\Models\SourceRevision;
use JiraRestApi\IssueLink\IssueLink;
use JiraRestApi\IssueLink\IssueLinkService;

/**
 * Head merge sources to head
 *
 * @category Console_Command
 */
class HeadMergeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "sources:head-merge";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create head merge TTS tasks for sources not merged in head";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $patches = PatchesHeadMerge::where('processed_headmerge', 0)->get();

        $this->info("Found {$patches->count()} not processed " . Str::plural('patch', $patches->count()));

        foreach ($patches as $patch) {
            $this->info("Getting sources data for patch {$patch->patch_id} ...");

            $data = collect(
                json_decode(json_encode($this->getData($patch->patch_id)), JSON_OBJECT_AS_ARRAY)
            );

            $revisions = implode(", ", $data->pluck('rev_id')->all());
            $this->info(
                "Found {$data->count()} not merged " . Str::plural('revision', $data->count()) . " - {$revisions}"
            );

            $data = $data->groupBy('username');

            foreach ($data as $username => $sources) {
                try {
                    $issue = $this->createIssue($username, $sources);
                    $this->linkIssue($issue->key, $sources->first()['tts_id']);

                    SourceRevision
                        ::whereIn('rev_id', $sources->pluck('rev_id')->all())
                        ->update(['requested_head_merge' => 1]);

                    $patch->tts_keys_headmerge = trim("{$patch->tts_keys_headmerge}, {$issue->key}", ', ');

                    $this->info("New issue {$issue->key} was created and assigned to user {$username}");
                    $this->info("Issue {$issue->key} was linked to {$sources->first()['tts_id']}");
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    $this->error($e->getMessage());
                }
            }

            $patch->processed_headmerge = 1;
            $patch->save();
        }
    }

    /**
     * Get data
     *
     * @param int $patchId
     *
     * @return array
     */
    protected function getData(int $patchId) : array
    {
        /*
         * Min date was confirmed by Iskren Marinov
         * under subject SUDF commit old branches
         */
        $minDate = '2018-03-28';

        return DB::select(
            "
            SELECT U.username,
                   SR.rev_id,
                   M.name AS source_file,
                   SR.revision,
                   P.id AS patch_id,
                   PRJ.id AS project_id,
                   I.tts_id,
                   PRJ.name AS project_name,
                   PRJ.tts_dev_project_key
              FROM patches P
              JOIN patch_requests PR ON P.patch_request_id=PR.id
              JOIN issues I ON PR.issue_id=I.id
              JOIN projects PRJ ON I.project_id=PRJ.id
              JOIN modif_to_patch MP ON P.id=MP.patch_id
              JOIN modifications M ON MP.modif_id=M.id
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
              WHERE P.id=?
              AND M.type_id='source'
              AND SR.rev_registration_date>=?
              AND (SR.requested_head_merge IS NULL OR SR.requested_head_merge<>1)
              AND M.version LIKE '%.%.%'
              AND MSR.rev_id IS NULL
              AND BSR.rev_id IS NULL
              ORDER BY P.migr_sequence_N DESC;
            ",
            [$patchId, $minDate]
        );
    }

    /**
     * Get TTS project dev key
     *
     * @param Collection $sources
     *
     * @return string|null
     *
     * @throws \Exception
     */
    protected function getTtsDevProjectKey(Collection $sources) : ?string
    {
        $ttsDevProjectKey = $sources->first()['tts_dev_project_key'];

        // If there is not tts dev project key send mail and exit
        if (!$ttsDevProjectKey) {
            $projectName = $sources->first()['project_name'];

            // Notify for missing dev key
            Mail::queue(
                (new MissingProjectDevKeyMail($projectName))->onQueue('mails')
            );

            throw new \Exception("TTS dev project key not found for {$projectName}!");
        }

        return $ttsDevProjectKey;
    }

    /**
     * Get issue data
     *
     * @param string $username
     * @param Collection $sources
     *
     * @return IssueField
     *
     * @throws \Exception
     */
    protected function getIssue(string $username, Collection $sources) : IssueField
    {
        $ttsId         = $sources->first()['tts_id'];

        $issueField = new IssueField();

        // Get sources list
        $sources = implode(
            PHP_EOL,
            $sources->map(function ($item) {
                return "{$item['source_file']} - {$item['revision']}";
            })->all()
        );

        $issueField
            ->setProjectKey('CVSHEAD')
            ->setSummary("Commit on Head the changes done in {$ttsId}")
            ->setAssigneeName($username)
            ->setIssueType('Short Task')
            ->setPriorityName('Normal')
            ->setDescription("
                The test of task {$ttsId} is completed OK. Please commit your changes in the HEAD revision.
                
                *Sources:* 
                {$sources}
            ")
            ->addLabel('MMPI_AUTO');

        // Set specification
        $issueField->addCustomField('customfield_10140', 'n/a');

        // Set sub-project
        $issueField->addCustomField('customfield_10123', 'n/a');

        // Set Milestone
        $issueField->addCustomField('customfield_10530', ['value' => 'Installation']);
        
        // Set Codix status
        $issueField->addCustomField('customfield_10601', ['value' => 'Under Investigation']);

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
    
    /**
     * Get link data
     *
     * @param type $inwardIssue
     * @param type $outwardIssue
     * @return IssueLink
     */
    protected function getLink($inwardIssue, $outwardIssue)
    {
        $issueLink = new IssueLink();
        
        $issueLink->setInwardIssue($inwardIssue)
                ->setOutwardIssue($outwardIssue)
                ->setLinkTypeName("Relate")
                ->setComment("Automatically linked to task {$outwardIssue}");
        
        return $issueLink;
    }
    
    /**
     * Link issues
     *
     * @param type $inwardIssue
     * @param type $outwardIssue
     *
     */
    protected function linkIssue($inwardIssue, $outwardIssue)
    {
        $issueLink = $this->getLink($inwardIssue, $outwardIssue);
        
        $issueLinkService = new IssueLinkService();
        $linkedIssue = $issueLinkService->addIssueLink($issueLink);
        
        return $linkedIssue;
    }
}
