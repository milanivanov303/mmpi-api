<?php

namespace Modules\Gitlab\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');
        $params = [];

        if ($request->has('include_subgroups')) {
            $params['include_subgroups'] = $request->get('include_subgroups') === 'true' ? true : false;
        }

        $params['per_page'] = 100;

        $projects = app('GitlabApi', $config)->groups()->projects($request->get('groupId'), $params);

        if ($request->has('topic')) {
            return app(Project::class)->projectsByTopic($request->get('topic'), $projects);
        }

        return $projects;
    }
    
    public function commitFiles(Request $request, $sha) : array
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');
        $response = app('GitlabApi', $config)->repositories()->diff($request->get('repo'), $sha);

        if (is_array($response) && array_key_exists('diff', $response[0])) {
            unset($response[0]['diff']);
        }

        return $response;
    }

    public function getCommit(Request $request, $sha)
    {
        if (!$request->has('repoUrl')) {
            throw new HttpException(400, 'Missing gitlab server url');
        }

        $config['repoUrl'] = $request->get('repoUrl');

        return app('GitlabApi', $config)->repositories()->commit($request->get('repo'), $sha);
    }
}
