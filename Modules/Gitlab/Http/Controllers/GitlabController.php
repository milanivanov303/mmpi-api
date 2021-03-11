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
        $httpClient = new \GuzzleHttp\Client([
            'verify' => false
        ]);

        $this->client = \Gitlab\Client::createWithHttpClient($httpClient);
        $this->client->setUrl(env('GITLAB_HOST'));
        $this->client->authenticate(env('GITLAB_TOKEN'), \Gitlab\Client::AUTH_HTTP_TOKEN);
    }
    
    public function projects($visibility)
    {
        $projects = $this->client->projects()->all(['visibility' => $visibility]);
        return $projects;
    }
    
    public function showProject(Request $request)
    {
        $project = $this->client->projects()->show($request->repo);
        return $project;
    }
    
    public function branches(Request $request)
    {
        $branches = $this->client->repositories()->branches($request->repo);
        return $branches;
    }
    
    public function branch(Request $request, $name)
    {
        $branch = $this->client->repositories()->branch($request->repo, $name);
        return $branch;
    }

    public function getRepoTags(Request $request)
    {
        $repoTags = $this->client->repositories()->tags($request->repo);
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
        
        $commits = $this->client->repositories()->commits($request->repo, $params);
        return $commits;
    }
    
    public function commitRefs(Request $request, $sha)
    {
        $refs = $this->client->repositories()->commitRefs($request->repo, $sha);
        return $refs;
    }
}
