<?php

namespace Modules\Gitlab\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GitlabController extends Controller
{
    public function projects($visibility)
    {
        $projects = app('GitlabApi')->projects()->all(['visibility' => $visibility, 'per_page' => 100]);
        return $projects;
    }

    public function showProject(Request $request)
    {
        $project = app('GitlabApi')->projects()->show($request->repo);
        return $project;
    }
    
    public function branches(Request $request)
    {
        $branches = app('GitlabApi')->repositories()->branches($request->repo);
        return $branches;
    }
    
    public function branch(Request $request, $name)
    {
        $branch = app('GitlabApi')->repositories()->branch($request->repo, $name);
        return $branch;
    }

    public function getRepoTags(Request $request)
    {
        $repoTags = app('GitlabApi')->repositories()->tags($request->repo);
        return $repoTags;
    }
    
    /**
     * Get commits based on repository id and branch. If no branch is supplied gets commits from default branch.
     * @param int|string $projectId
     * @param Request $request
     * @return array
     */
    public function commits(Request $request) : array
    {
        $params = [];
        if ($request->branch) {
            $params['ref_name'] = $request->branch;
        }
        
        if ($request->since) {
            $params['since'] = new \DateTime($request->since);
        }
        
        $commits = app('GitlabApi')->repositories()->commits($request->repo, $params);
        return $commits;
    }
    
    public function commitRefs(Request $request, $sha)
    {
        $refs = app('GitlabApi')->repositories()->commitRefs($request->repo, $sha);
        return $refs;
    }

    public function namespaces()
    {
        $namespaces = app('GitlabApi')->namespaces()->all(['per_page' => 100]);
        return $namespaces;
    }

    public function groups()
    {
        $groups = app('GitlabApi')->groups()->all(['per_page' => 100]);
        return $groups;
    }

    public function groupProjects(Request $request)
    {
        $params = [];
        if ($request->include_subgroups) {
            $params['include_subgroups'] = $request->include_subgroups === 'true' ? true : false;
        }

        $params['per_page'] = 100;

        $groups = app('GitlabApi')->groups()->projects($request->groupId, $params);
        return $groups;
    }
}
