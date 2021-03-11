<?php

namespace Modules\Gitlab\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GitlabController extends Controller
{
    protected $client;
    /**
     * Create a new controller instance.
     *
     * @param BranchRepository $repository
     * @return void
     */
    public function __construct()
    {
        $this->client = new \Gitlab\Client();
        $this->client->setUrl(env('GITLAB_HOST'));
        $this->client->authenticate(env('GITLAB_TOKEN'), \Gitlab\Client::AUTH_HTTP_TOKEN);
    }
    
    public function projects($visibility)
    {
        $projects = $this->client->projects()->all(['visibility' => $visibility]);
        return $projects;
    }
    
    public function showProject($projectId)
    {
        $project = $this->client->projects()->show($projectId);
        return $project;
    }
    
    public function branches($projectId)
    {
        $branches = $this->client->repositories()->branches($projectId);
        return $branches;
    }
    
    public function branch($projectId, $name)
    {
        $branch = $this->client->repositories()->branch($projectId, $name);
        return $branch;
    }

    public function getRepoTags($repoId)
    {
        $repoTags = $this->client->repositories()->tags($repoId);
        return $repoTags;
    }
    
    /**
     * Get commits based on repository id and branch. If no branch is supplied gets commits from master (not sure).
     * @param int|string $projectId
     * @param Request $request
     * @return array
     */
    public function commits($projectId, Request $request) : array
    {
        $params = [];
        if ($request->ref_name) {
            $params['ref_name'] = $request->ref_name;
        }
        
        if ($request->since) {
            $params['since'] = new \DateTime($request->since);
        }
        
        $commits = $this->client->repositories()->commits($projectId, $params);
        return $commits;
    }
    
    public function commitRefs($projectId, $sha)
    {
        $refs = $this->client->repositories()->commitRefs($projectId, $sha);
        return $refs;
    }
}
