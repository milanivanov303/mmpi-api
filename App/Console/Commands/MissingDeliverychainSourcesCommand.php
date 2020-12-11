<?php

namespace App\Console\Commands;

use App\Models\PatchesHeadMerge;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use App\Mail\MissingProjectDevKeyMail;
use JiraRestApi\IssueLink\IssueLink;
use JiraRestApi\IssueLink\IssueLinkService;
use App\Models\EnumValue;

/**
 * Add missing sources to delivery chain
 *
 * @category Console_Command
 */
class MissingDeliverychainSourcesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "sources:missing-deliverychain";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create TTS tasks for sources not installed on DC";
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //$patches = PatchesHeadMerge::where('processed_dcsync', 0)->get();  For final version
        // Test behaviour - project SUDF, 3 months back
        $patches = PatchesHeadMerge
                ::select(['patches_head_merge.*', 'DC.title as dc_title', 'EV.key as dc_role'])
                ->join('patches as P', 'patches_head_merge.patch_id', '=', 'P.id')
                ->join('projects as PR', 'PR.id', '=', 'P.project_id')
                ->join('delivery_chains as DC', 'DC.id', '=', 'P.delivery_chain_id')
                ->join('enum_values as EV', 'DC.dc_role', '=', 'EV.id')
                ->where('PR.name', 'SUDF')
                ->where('patches_head_merge.processed_dcsync', 0)
                ->where('patches_head_merge.modified_on', '>=', '2020-07-01 00:00:00')
                ->get();

        $this->info("Found {$patches->count()} not processed " . Str::plural('patch', $patches->count()));
        
        foreach ($patches as $patch) {
            if ($patch->dc_role !== 'dc_rel') {
                $this->info("Getting sources data for patch {$patch->patch_id} ...");

                $data = collect(
                    json_decode(json_encode($this->getData($patch->patch_id)), JSON_OBJECT_AS_ARRAY)
                );

                $strings = [];
                foreach ($data as $sourceResult) {
                    $strings[] = $sourceResult['name'] . " " . $sourceResult['version']
                            . " for DC " . $sourceResult['dc_needed'];
                }

                $sources = implode(", ", $strings);
                $this->info(
                    "Found {$data->count()} not installed " . Str::plural('source', $data->count()) . " - {$sources}"
                );

                $data = $data->groupBy('patch_id');

                foreach ($data as $patchId => $sources) {
                    try {
                        $issue = $this->createIssue($sources->first()['send_to_pc'], $sources);
                        $this->info("New issue {$issue->key} was created and assigned to user "
                        . "{$sources->first()['send_to_pc']}");

                        $this->linkIssue($issue->key, $sources->first()['link_to_tts']);
                        $this->info("Issue {$issue->key} was linked to {$sources->first()['link_to_tts']}");

                        $patch->tts_keys_dcsync = trim("{$patch->tts_keys_dcsync}, {$issue->key}", ', ');
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                        $this->error($e->getMessage());
                    }
                }
            }
            $patch->processed_dcsync = 1;
            $patch->save();
        }
    }
    
    /**
     * Get data
     *
     * @param int $patchId
     * @return array
     */
    protected function getData(int $patchId) : array
    {
        $dcStatus = app(EnumValue::class)
                ::where('type', 'active_inactive')
                ->where('key', 'active')
                ->value('id');
        
        $dcTypeId = app(DB::class)
                ::table('delivery_chain_types')
                ->where('type', 'IMX')
                ->value('id');
        
        $hotfix = app(EnumValue::class)
                ::where('type', 'delivery_chain_role')
                ->where('key', 'dc_hf')
                ->value('id');
        
        $validation = app(EnumValue::class)
                ::where('type', 'delivery_chain_role')
                ->where('key', 'dc_val')
                ->value('id');
        
        return DB::select(
            "SELECT PO.id as patch_id,
               U.username as added_by,
               MO.name,
               MO.version,
               DC_P.title as dc_patch,
               PRJO.project_id,
               PR.name as project_name,
               PR.tts_dev_project_key,
               I.tts_id as link_to_tts,
               DCN.title as dc_needed,
               UO.username as send_to_pc
            FROM patches PO
            JOIN modif_to_patch MPO ON PO.id=MPO.patch_id
            JOIN modifications MO ON MPO.modif_id=MO.id AND MO.version IS NOT NULL
            JOIN issues I ON MO.issue_id=I.id
            JOIN users U ON IFNULL(MO.updated_by_id, MO.created_by_id)=U.id
            JOIN delivery_chains DC_P ON PO.delivery_chain_id=DC_P.id
            JOIN project_to_delivery_chain PRJO ON DC_P.id=PRJO.delivery_chain_id
            JOIN users_prjs_roles UPRO ON (PRJO.project_id=UPRO.project_id AND UPRO.role_id='pc')
            JOIN users UO ON UPRO.user_id=UO.id
            JOIN project_to_delivery_chain PRJ ON (PRJO.project_id = PRJ.project_id AND PRJ.delivery_chain_id NOT IN 
                                            (SELECT DC.id
                                                FROM delivery_chains DC
                                                JOIN project_to_delivery_chain PDC ON DC.id=PDC.delivery_chain_id
                                                JOIN patches P ON DC.id=P.delivery_chain_id
                                                JOIN v_current_patch_status PS ON P.id=PS.patch_id
                                                JOIN enum_values EV_PS 
                                                    ON (PS.patch_status=EV_PS.id 
                                                        AND EV_PS.type='patches_status_history_status' 
                                                        AND EV_PS.`key` NOT IN ('cancelled', 'rejected'))
                                                JOIN modif_to_patch MP ON P.id=MP.patch_id
                                                JOIN modifications M ON MP.modif_id=M.id
                                                WHERE PDC.project_id=PRJ.project_id
                                                AND MO.name=M.name
                                                AND MO.version=M.version))
            JOIN delivery_chains DCN ON PRJ.delivery_chain_id=DCN.id
            JOIN projects PR ON PO.project_id=PR.id
            WHERE PO.id=?
            AND DCN.status=?
            AND DCN.type_id=?
            AND IF(DC_P.dc_role=?, DCN.dc_role<>?, DCN.dc_role NOT IN (?, ?));
            ",
            [$patchId, $dcStatus, $dcTypeId, $hotfix, $validation, $validation, $hotfix]
        );
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
        $ttsId         = $sources->first()['link_to_tts'];
        $projectKey    = $this->getTtsDevProjectKey($sources);

        $issueField = new IssueField();

        // Get sources list
        $sources = implode(
            PHP_EOL,
            $sources->map(function ($item) {
                return "{$item['name']} - {$item['version']}"
                . " added by {$item['added_by']} to be delivered {$item['dc_needed']}";
            })->all()
        );

        $issueField
            ->setProjectKey($projectKey)
            ->setSummary("Synchronization of the changes done in {$ttsId}")
            ->setAssigneeName($username)
            ->setIssueType('Short Task')
            ->setPriorityName('Normal')
            ->setDescription("
                Synchronization of delivery chains needed for the following sources.
                
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
