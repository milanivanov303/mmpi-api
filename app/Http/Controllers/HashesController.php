<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Hashes\HashCommit;
use App\Models\Hashes\HashChain;

/**
 * Manage hashes
 *
 */
class HashesController extends Controller
{
    /**
     * The user model instance.
     */
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param HashCommit $model
     * @return void
     */
    public function __construct(HashCommit $model)
    {
        $this->model = $model;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @param int $hash_id
     * @return array
     */
    protected function rules($hash_id = null)
    {
        return [
            'branch' => 'required',
            'owner'  => 'required|string|exists:users,username',
            'rev'    => 'required|string|unique:hash_commits,hash_rev,' . $hash_id
        ];
    }

    /**
     * Create new hash
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->rules());

        $hash = new HashCommit();
        return $this->save($hash, $request->json()->all(), 201);
    }

    /**
     * Update the specified hash.
     *
     * @param  Request  $request
     * @param  string  $hash_rev
     * @return Response
     * @throws \Throwable)
     */
    public function update(Request $request, $hash_rev)
    {
        $hash = $this->model->where('hash_rev', $hash_rev)->firstOrFail();
        
        $this->validate($request, $this->rules($hash->id));
        
        //$request->json()->remove('rev');
                
        return $this->save($hash, $request->json()->all());
    }
    
    /**
     * Delete the specified user.
     *
     * @param  string  $hash_rev
     * @return Response
     */
    public function delete($hash_rev)
    {
        $this->model->where('hash_rev', $hash_rev)->destroy($hash_rev);
        return response('Deleted', 204);
    }

    /**
     * Retrieve the hash for the given revision.
     *
     * @param  int  $hash_rev
     * @return Response
     */
    public function getOne($hash_rev)
    {
        return $this->model->where('hash_rev', $hash_rev)->firstOrFail();
    }
    
    /**
     * Retrieve hashes list.
     *
     * @param Request $request
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getMany(Request $request)
    {
        return $this->model->setFilters($request)->get();
    }
    
    /**
     * Save hash and it's relations
     *
     * @param HashCommit $hash
     * @param array $data
     * @return Response
     * @throws \Throwable
     */
    protected function save($hash, $data, $status = 200)
    {
        $hash->fill($data);
        $hash->saveOrFail();
        
        // save hash files
        if (isset($data['files'])) {
            $this->saveFiles($hash, $data['files']);
        }
        
        // save hash chains
        if (isset($data['chains'])) {
            $this->saveChains($hash, $data['chains']);
        }
        
        $hash->load(['files', 'chains', 'owner']);
        
        return response()->json($hash->toArray(), $status);
    }
    
    /**
     * Save hash files
     *
     * @param HashCommit $hash
     * @param array $files
     *
     * @throws \Throwable
     */
    private function saveFiles($hash, $files)
    {
        // delete old files before setting new ones
        $hash->files()->delete();
         
        $hash->files()->createMany(
            array_map(function ($file_name) {
                return ['file_name' => $file_name];
            }, $files)
        );
    }
    
    /**
     * Save hash chains
     *
     * @param HashCommit $hash
     * @param array $chains
     *
     * @throws \Throwable
     */
    private function saveChains($hash, $chains)
    {
        // delete old chains before setting new ones
        $hash->chains()->delete();

        $hash->chains()->createMany(
            array_map(function ($chain_name) {
                $chain = HashChain::where('chain_name', $chain_name)->firstOrFail();
                return ['hash_chain_id' => $chain->id];
            }, $chains)
        );
    }
}
