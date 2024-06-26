<?php

namespace Modules\Gitlab\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Gitlab\Helpers\GitlabHelper;
use Modules\Gitlab\Models\Project;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GitlabController extends Controller
{
    public function projects(Request $request, $visibility)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        return app('GitlabApi', $config)->projects()->all(['visibility' => $visibility, 'per_page' => 100]);
    }

    public function showProject(Request $request)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        return app('GitlabApi', $config)->projects()->show($request->get('repo'));
    }
    
    public function branches(Request $request)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        return app('GitlabApi', $config)->repositories()->branches($request->get('repo'), ['per_page' => 100]);
    }
    
    public function branch(Request $request, $name)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        return app('GitlabApi', $config)->repositories()->branch($request->get('repo'), $name);
    }

    public function getRepoTags(Request $request)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        return app('GitlabApi', $config)->repositories()->tags($request->get('repo'));
    }
    
    /**
     * Get commits based on repository id and branch. If no branch is supplied gets commits from default branch.
     * @param int|string $projectId
     * @param Request $request
     * @return array
     */
    public function commits(Request $request) : array
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');
        $params = [];

        if ($request->has('branch')) {
            $params['ref_name'] = $request->get('branch');
        }
        
        if ($request->has('since')) {
            $params['since'] = new \DateTime($request->get('since'));
        }

        if ($request->has('until')) {
            $params['until'] = new \DateTime($request->get('until'));
        }

        return app('GitlabApi', $config)->repositories()->commits($request->get('repo'), $params);
    }
    
    public function commitRefs(Request $request, $sha)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');
        $params = [];
        if ($request->has('per_page')) {
            $params['per_page'] = (int)$request->get('per_page');
        }

        return app('GitlabApi', $config)->repositories()->commitRefs($request->get('repo'), $sha, $params);
    }

    public function namespaces(Request $request)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        return app('GitlabApi', $config)->namespaces()->all(['per_page' => 100]);
    }

    public function groups(Request $request)
    {
        $config = [];
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        if ($request->has('as_user')) {
            $config['headers']['sudo'] = $request->get('as_user');
        }

        return app('GitlabApi', $config)->groups()->all(['per_page' => 100]);
    }

    public function groupProjects(Request $request)
    {
        if (!$request->has('repoUrl') || !$request->has('groupId')) {
            throw new HttpException(400, 'Missing gitlab server url or group id');
        }

        $config['url'] = $request->get('repoUrl');
        $projects = app('NativeGitlabApi', $config)->get(
            "groups/{$request->get('groupId')}/projects",
            ['per_page' => 100]
        );

        if ($projects->isUnsuccessful()) {
            throw new HttpException(400, 'Unsuccessful request');
        }

        if ($request->has('topic')) {
            return app(Project::class)->projectsByTopic($request->get('topic'), $projects->getData());
        }

        return $projects->getData();
    }
    
    public function commitFiles(Request $request, $sha) : array
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['url'] = $request->get('repoUrl');
        $project = urlencode($request->get('repo'));

        $diff = app('NativeGitlabApi', $config)->get("projects/{$project}/repository/commits/{$sha}/diff");

        if ($diff->isUnsuccessful()) {
            throw new HttpException(400, 'Unsuccessful request');
        }

        $commitDiff = $diff->getData();

        if (isset($commitDiff) && is_array($commitDiff)) {
            return array_map(function ($n) {
                if (array_key_exists('diff', $n)) {
                    unset($n['diff']);
                }
                return $n;
            }, $commitDiff);
        }

        return [];
    }

    public function getCommit(Request $request, $sha)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        return app('GitlabApi', $config)->repositories()->commit($request->get('repo'), $sha);
    }

    /**
     * Get project pipelines
     * @param int|string $projectId
     * @param Request $request
     * @return array
     */
    public function getPipeline(Request $request, $project_id, $pipeline_id)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        return  app('GitlabApi', $config)->projects()->pipeline($project_id, $pipeline_id);
    }
}
