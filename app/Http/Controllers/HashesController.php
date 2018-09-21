<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Hashes\HashCommit;
use App\Models\Hashes\HashCommitFile;
//use App\Models\Hashes\HashCommitToChain;
use App\Traits\Filterable;

class HashesController extends Controller
{
    use Filterable;

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
     * Retrieve users list.
     *
     * @param Request $request
     * @return Response
     */

    /**
     * @param Request $request
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function many(Request $request)
    {
        $filters = $this->getFilters($request);
        return $this->model->with(['files', 'chains'])->where($filters)->get();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        /*
            {  
               "branch":"default",
               "chains":[  
                  "coface_coin_release",
                  "bacolumbia_imx_release"
               ],
               "description":"IXDEV-1650 e_honor_param backend\n\nadd MOLO as mvn profile",
               "files":[  
                  "etc/configs/MOLOTCWALLET/imx_backend.properties",
                  "etc/configs/MOLOTCWALLET/imx_backend.xml",
                  "etc/configs/MOLOTCWALLET/wallet/cwallet.sso",
                  "etc/configs/MOLOTCWALLET/wallet/tnsnames.ora",
                  "pom.xml"
               ],
               "merge_branch":"_DEV_IXDEV-1763 e_honor_param backend",
               "module":"imx_be",
               "owner":"astamenov <astamenov@codix.bg>",
               "repo_path":"/extranet/hg/v9_be",
               "repo_url":"http://lemon.codixfr.private:6002/v9_be",
               "rev":"5267baed17ce97750b2a9a489eaf1095678e6151"
            }
        */

        $data = $request->json()->all();
        
        $hash = new HashCommit();
        
        $hash->repo_branch        = $data['branch'];
        $hash->commit_description = $data['description'];
        $hash->repo_merge_branch  = $data['merge_branch'];
        $hash->repo_module        = $data['module'];
        $hash->committed_by       = $data['owner'];
        $hash->repo_path          = $data['repo_path'];
        $hash->repo_url           = $data['repo_url'];
        $hash->hash_rev           = $data['rev'];
        //$hash->repo_timestamp     = $data['']; ??
            
        $hash->saveOrFail();
        
        foreach ($data['files'] as $file) {
            $hashFile = new HashCommitFile();
            $hashFile->hash_commit_id = $hash->id;
            $hashFile->file_name      = $file;
            $hashFile->saveOrFail();
        }
           
        return response()->json($hash, 201);
    }

    /**
     * Update the specified user.
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //$this->validate($request, [
        //    'name' => 'required',
        //    'email' => 'required|email|unique:users'
        //]);

        $user = $this->model->findOrFail($id);

        $user->fill($request->json()->all());

        if ($user->saveOrFail()) {
            return response()->json($user);
        }
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
        return $this->model->with(['files', 'chains'])->findOrFail($id);
    }
}
