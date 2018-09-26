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
     * @return array
     */
    protected function rules()
    {
        return [
            'branch' => 'required',
            'owner' => 'required|string|exists:users,username'
        ];
    }

    /**
     * Retrieve users list.
     *
     * @param Request $request
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function many(Request $request)
    {
        return $this->model->setFilters($request)->get();
    }

    /**
     * Create new hash
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     * 
     * @OA\Post(
     *     path="/hashes",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->rules());

        $hash = new HashCommit();
        return $this->save($hash, $request->json()->all()); 
    }

    /**
     * Update the specified hash.
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     * @throws \Throwable
     * @OA\Put(
     *     path="/hashes",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    public function update(Request $request, $id)
    {        
        $this->validate($request, $this->rules());
        
        $hash = $this->model->findOrFail($id);   
        return $this->save($hash, $request->json()->all()); 
    }
    
    /**
     * Save hash and it's relations 
     * 
     * @param HashCommit $hash
     * @param array $data
     * @return Response
     * @throws \Throwable
     */
    protected function save($hash, $data)
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
        
        $hash->load(['files', 'chains', 'user']);
        
        return response()->json($hash->toArray());
    }

    /**
     * Delete the specified user.
     *
     * @param  string  $id
     * @return Response
     */
    public function delete($id)
    {
        $this->model->destroy($id);
        return response('Deleted', 204);
    }

    /**
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->model->findOrFail($id);
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
