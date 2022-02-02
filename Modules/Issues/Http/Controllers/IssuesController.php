<?php

namespace Modules\Issues\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * If issue not found overwrite route.
     *
     * @param Request $request
     * @param mixed ...$tts_id
     * @return Issue|void
     */
    public function getOne(Request $request, ...$tts_id)
    {
        try {
            parent::getOne($request, ...$tts_id);
        } catch (ModelNotFoundException $e) {
            return self::importTtsIssue($tts_id);
        }
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
                $ttsId          = $issue->fields->parent->key;
                $parentSubject  = $issue->fields->parent->fields->summary;
                $parentPriority = $issue->fields->parent->fields->priority->name;

                //get parent issue from mmpi
                $parentMmpiIssue = Issue::where('tts_id', '=', $ttsId)->first();

                //Check if parent issue exist in mmpi, otherwise get it from jira
                if (!$parentMmpiIssue) {
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
                    $newParentMmpiIssue = Issue::create($parentIssueArr);
                }
                $parentMmpiIssueId = isset($newParentMmpiIssue->id) ? $newParentMmpiIssue->id : $parentMmpiIssue->id;
            }

            //If sub issue get parent id
            $parentIssueId = isset($parentMmpiIssueId) ? $parentMmpiIssueId : null;
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
             $ttsIssue = Issue::create($issueArr);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }

        return $ttsIssue;
    }
}
