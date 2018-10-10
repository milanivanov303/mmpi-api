<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Hashes\HashCommit;
use App\Models\Hashes\HashChain;
use Illuminate\Support\Facades\DB;

/**
 * Manage hashes
 *
 */
class HashesController extends Controller
{
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
     * Create new hash
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $hash = new HashCommit();
        return $this->save(new HashCommit, $request->json()->all(), 201);
    }

    /**
     * Update the specified hash.
     *
     * @param  Request  $request
     * @param  string  $hash_rev
     * @return Response
     */
    public function update(Request $request, $hash_rev)
    {
        $hash = $this->model->where('hash_rev', $hash_rev)->firstOrFail();
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
        DB::transaction(function () use ($hash_rev) {
            $hash = $this->model->where('hash_rev', $hash_rev)->firstOrFail();

            $hash->files()->delete();
            $hash->chains()->delete();

            $hash->delete();
        });

        return response('Hash deleted successfully', 204);
    }

    /**
     * Retrieve the hash for the given revision.
     *
     * @param  int  $hash_rev
     * @return Response
     */
    public function getOne($hash_rev)
    {
        return $this->output(
            $this->model->where('hash_rev', $hash_rev)->firstOrFail()
        );
    }
    
    /**
     * Retrieve hashes list.
     *
     * @param Request $request
     * @return HashCommit[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getMany(Request $request)
    {
        if ($request->input('page')) {
            $data = $this->model->setFilters($request)->paginate($request->input('per_page'));
        } else {
            $data = $this->model->setFilters($request)->get();
        }

        return $this->output($data);
    }
    
    /**
     * Save hash and it's relations
     *
     * @param HashCommit $hash
     * @param array $data
     * @return Response
     */
    protected function save($hash, $data, $status = 200)
    {
        $hash->fill($data);
        
        DB::transaction(function () use ($hash, $data) {
            $hash->saveOrFail();

            // save hash files
            if (isset($data['files'])) {
                $this->saveFiles($hash, $data['files']);
            }

            // save hash chains
            if (isset($data['chains'])) {
                $this->saveChains($hash, $data['chains']);
            }
        });
        
        return $this->output($hash, $status);
    }
    
    /**
     * Save hash files
     *
     * @param HashCommit $hash
     * @param array $files
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
