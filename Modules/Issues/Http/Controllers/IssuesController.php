<?php

namespace Modules\Issues\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use JiraRestApi\Issue\IssueService;
use Modules\Issues\Models\Issue;
use Modules\Issues\Repositories\IssueRepository;
use Modules\Projects\Models\Project;

class IssuesController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param IssueRepository $repository
     * @return void
     */
    public function __construct(IssueRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Import new Issue from TTS.
     *
     * @param $tts_id
     * @return Issue
     */
    public function importTtsIssue($tts_id)
    {
        //Get issue from mmpi if exist
        $mmpiIssue = Issue::where('tts_id', '=', $tts_id)->get();

        if (!$mmpiIssue->isEmpty()) {
            return $mmpiIssue;
        }

        try {
            // if issue doesn't exist in mmpi get it from TTS
            $issueService = new IssueService();
            $issue        = $issueService->get($tts_id);

            $projectName  = trim($issue->fields->project->name, '_');
            $project      = Project::where('name', '=', $projectName)->first();
            $isSubTask    = $issue->fields->issuetype->subtask;
            $subject      = $issue->fields->summary;
            $priority     = $issue->fields->priority->name;
            $createdOn    = Carbon::now()->format('Y-m-d H:i:s');

            // Check if parent issue exist
            if ($isSubTask === true) {
                $parentIssueId  = $issue->fields->parent->id;
                $ttsId          = $issue->fields->parent->key;
                $parentSubject  = $issue->fields->parent->fields->summary;
                $parentPriority = $issue->fields->parent->fields->priority->name;

                $parentIssueArr = [
                    'project_id'        => $project->id,
                    'subject'           => $parentSubject,
                    'tts_id'            => $ttsId,
                    'jiraissue_id'      => $issue->id,
                    'parent_issue_id'   => null,
                    'created_on'        => $createdOn,
                    'dev_instance_id'   => null,
                    'priority'          => $parentPriority
                ];

                //Insert parent issue into mmpi db
                Issue::insert($parentIssueArr);
            }

            $parentIssueId = isset($parentIssueId) ? $parentIssueId : null;
            $issueArr = [
                'project_id'        => $project->id,
                'subject'           => $subject,
                'tts_id'            => $issue->key,
                'jiraissue_id'      => $issue->id,
                'parent_issue_id'   => $parentIssueId,
                'created_on'        => $createdOn,
                'dev_instance_id'   => null,
                'priority'          => $priority
            ];

            //Insert sub issue into mmpi db
            $ttsIssue = new Issue();
            $ttsIssue->fill($issueArr)->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
        return $ttsIssue;
    }
}
