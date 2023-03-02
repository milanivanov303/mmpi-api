<?php

namespace Modules\Issues\Http\Controllers;

use App\Models\EnumValue;
use App\Models\User;
use Carbon\Carbon;
use http\Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JiraRestApi\Issue\IssueService;
use Modules\Issues\Models\Issue;
use Modules\Issues\Repositories\IssueRepository;
use Modules\Projects\Models\Project;
use App\Traits\Ctts;
use Modules\SourceRevisions\Models\SourceRevision;
use Modules\Sources\Models\Source;

class IssuesController extends Controller
{
    use Ctts;

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
     * @param mixed ...$parameters
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne(Request $request, ...$parameters)
    {
        try {
            $mmpiResult = parent::getOne($request, ...$parameters);
            if ($mmpiResult) {
                return $mmpiResult;
            }
        } catch (ModelNotFoundException $e) {
            return self::importTtsIssue($parameters);
        }
    }

    /**
     * Import new Issue from TTS.
     *
     * @param $tts_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function importTtsIssue(array $tts_id)
    {
        try {
            // if issue doesn't exist in mmpi get it from TTS
            $issueService = new IssueService();
            $issue = $issueService->get($tts_id[0]);

            $projectName = trim($issue->fields->project->name);
            $projectName = ltrim($projectName, '_');
            $projectName = trim(preg_replace('/[-–][ \s]?([ \s]?[A-Z]{2,3}){1,2}$/', '', $projectName));

            $project = Project::where('name', '=', $projectName)->first();
            if (is_null($project)) {
                throw new \Exception("Project {$projectName} not exists in MMPI");
            }

            $isSubTask = $issue->fields->issuetype->subtask;
            $subject   = $issue->fields->summary;
            $priority  = $issue->fields->priority->name;
            $createdOn = Carbon::now()->format('Y-m-d H:i:s');

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
                    $parentMmpiIssue = Issue::create($parentIssueArr);
                }
            }
            //If sub issue get parent id
            $parentIssueId = $parentMmpiIssue->id ?? null;
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
            throw new ModelNotFoundException;
        }

        return response()->json($ttsIssue->toArray());
    }

    public function headmerge(string $tts_id)
    {
        Log::channel('headmerege')->debug("Checking issue {$tts_id}");
        $issue = Issue::where('tts_id', '=', $tts_id)->with('modifications.createdBy')->first();

        if (!$issue) {
            $result = "Issue {$tts_id} does not exist in MMPI";
            Log::channel('headmerege')->debug($result);
            return $result;
        }

        if ($issue->modifications->count() === 0) {
            $result = "No modifications are registered for issue {$tts_id}";
            Log::channel('headmerege')->debug($result);
            return $result;
        }

        $modifications = $issue->modifications->groupBy('type_id');

        if (isset($modifications['source'])) {
            $this->checkSourceModifications($modifications['source'], $tts_id);
        }

        $this->markAsDelivered($issue->modifications);

        return response()->json(['status' => 'ok']);
    }

    protected function markAsDelivered(Collection $modifications)
    {
        $autoUser = User::where('username', 'mmpi_auto')->pluck('id');
        $enum = EnumValue::where('type', 'modifications_status_history_status')
            ->where('key', 'checked_delivered')
            ->pluck('id');

        $insertData = [];
        $modifications->each(function ($item) use (&$insertData, $autoUser, $enum) {
            $insertData[] = [
                'modif_id'            => $item->id,
                'modification_status' => $enum[0],
                'user_id'             => $autoUser[0],
                'date'                => Carbon::now(),
                'comment'             => 'auto marked from headmerge for internal projects'
            ];
        });

        try {
            DB::table('modifications_status_history')->insert($insertData);
        } catch (\Exception $e) {
            Log::channel('headmerege')->debug($e->getMessage());
        }
    }

    /**
     * Check source modifications if there are branch modifications
     *
     * @param Collection $sources
     * @param string $tts_id
     */
    protected function checkSourceModifications(Collection $sources, string $tts_id)
    {
        $branchModifications = collect();
        $sources->each(function ($item) use ($branchModifications) {
            if (substr_count($item->version, '.') > 1) {
                $branchModifications->add($item);
            }
        });

        if ($branchModifications->count() > 0) {
            $branchModifications->groupBy('created_by_id')->each(function ($item, $userId) use ($tts_id) {
                try {
                    $newIssue = $this->createIssue($item[0]->createdBy->username, $item, $tts_id);
                    $this->linkIssue($newIssue->key, $tts_id);
                    Log::channel('headmerege')->debug("
                            New issue {$newIssue->key} was created and assigned to user {$item[0]->createdBy->username}
                        ");
                    Log::channel('headmerege')->debug(
                        "Issue {$newIssue->key} was linked to {$tts_id}"
                    );

                    $this->markSourceRevision($item);
                } catch (\Exception $e) {
                    Log::channel('headmerege')->debug($e->getMessage());
                }
            });
        }
    }

    /**
     * Mark corresponding records in table source_revision as requested_head_merge
     *
     * @param \Illuminate\Support\Collection $sourceRevisions
     */
    protected function markSourceRevision(\Illuminate\Support\Collection $sourceRevisions)
    {
        $revIds = [];
        $sourcesInfo = [];
        foreach ($sourceRevisions as $sourceRevision) {
            $pathInfo = pathinfo($sourceRevision->name);

            $sourceFile = Source::where('source_name', $pathInfo['basename'])
                ->where('source_path', $pathInfo['dirname'])->first();

            if ($sourceFile) {
                $sourcesInfo[] = [
                    'source_id' => $sourceFile->source_id,
                    'revision' => $sourceRevision->version
                ];
            }
        }

        if (!empty($sourcesInfo)) {
            foreach ($sourcesInfo as $sourceInfo) {
                $sourceRev = SourceRevision::where('source_id', $sourceInfo['source_id'])
                    ->where('revision', $sourceInfo['revision'])
                    ->first();

                if ($sourceRev) {
                    $revIds[] = $sourceRev->rev_id;
                }
            }
        }

        if (!empty($revIds)) {
            try {
                SourceRevision::whereIn('rev_id', $revIds)->update(['requested_head_merge' => 1]);
                $ids = implode(',', $revIds);
                Log::channel('headmerege')->debug("Table source_revision updated for {$ids}");
            } catch (\Exception $e) {
                Log::channel('headmerege')->debug($e->getMessage());
            }
        }
    }
}
