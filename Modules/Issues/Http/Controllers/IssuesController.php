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
use Modules\Modifications\Models\Modification;

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
            if (!$this->checkSourceModifications($modifications['source'], $tts_id)) {
                Log::channel('headmerege')->debug('end');
                return response()->json(['error' => 'error in method checkSourceModifications()']);
            }
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
            Log::channel('headmerege')->debug("Modifications are marked as delivered");
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
    protected function checkSourceModifications(Collection $sources, string $tts_id) : bool
    {
        $result = true;
        $branchModifications = collect();
        $sourceRevisionsToUpdate = [];

        $sources->each(function ($item) use ($branchModifications, &$sourceRevisionsToUpdate) {
            $headmergeStatus = $this->checkHeadmergeStatus($item);
            if (substr_count($item->version, '.') > 1 && $headmergeStatus) {
                $sourceRevisionsToUpdate[] = $headmergeStatus;
                $branchModifications->add($item);
            }
        });

        if ($branchModifications->count() > 0) {
            $branchModifications->groupBy('created_by_id')->each(function ($item) use ($tts_id, &$result) {
                try {
                    $username = $item[0]->createdBy->username;
                    if ($item[0]->createdBy->status != 1) { //check User if is not active
                        $manager = User::where('id', $item[0]->createdBy->manager_id)->get()->toArray();
                        $username = $manager[0]['username'];
                        if ($manager[0]['status'] != 1) { //check PMO if is not active
                            $username = 'cams_support';
                        }
                    }
                    $newIssue = $this->createIssue($username, $item, $tts_id);
                    Log::channel('headmerege')->debug("
                            New issue {$newIssue->key} was created and assigned to user {$item[0]->createdBy->username}
                        ");

                    $this->linkIssue($newIssue->key, $tts_id);
                    Log::channel('headmerege')->debug(
                        "Issue {$newIssue->key} was linked to {$tts_id}"
                    );
                } catch (\Exception $e) {
                    Log::channel('headmerege')->debug($e->getMessage());
                    $result =  false;
                }
            });

            if ($result) {
                $this->markSourceRevisions($sourceRevisionsToUpdate);
            }
        }

        return $result;
    }

    /**
     * @param Modification $source
     * @return integer
     */
    protected function checkHeadmergeStatus(Modification $source) : ?int
    {
        $result = [];
        $pathInfo = pathinfo($source->name);
        $sourceFile = Source::where('source_name', $pathInfo['basename'])
            ->where('source_path', $pathInfo['dirname'])->first();

        if ($sourceFile) {
            $result['source_id'] = $sourceFile->source_id;
            $result['revision'] = $source->version;
        }

        if (!empty($result)) {
            $sourceRev = SourceRevision::where('source_id', $result['source_id'])
                ->where('revision', $result['revision'])
                ->first();

            return $sourceRev && $sourceRev->requested_head_merge !== 1 ? $sourceRev->rev_id : null;
        }

        return null;
    }

    /**
     * Mark corresponding records in table source_revision as requested_head_merge
     *
     * @param array $sourceRevisionsToUpdate
     */
    protected function markSourceRevisions(array $sourceRevisionsToUpdate)
    {
        if (!empty($sourceRevisionsToUpdate)) {
            try {
                SourceRevision::whereIn('rev_id', $sourceRevisionsToUpdate)->update(['requested_head_merge' => 1]);
                $ids = implode(',', $sourceRevisionsToUpdate);
                Log::channel('headmerege')->debug("Table source_revision updated for {$ids}");
            } catch (\Exception $e) {
                Log::channel('headmerege')->debug($e->getMessage());
            }
        }
    }
}
