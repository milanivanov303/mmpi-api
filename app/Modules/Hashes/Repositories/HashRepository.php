<?php

namespace App\Modules\Hashes\Repositories;

use App\Modules\Hashes\Models\HashCommit;
use App\Repositories\RepositoryInterface;
use App\Repositories\AbstractRepository;
use App\Modules\Hashes\Models\HashChain;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Hashes\Services\DescriptionParser;

class HashRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Column to use on get/update/delete
     *
     * @var string
     */
    protected $primaryKey = 'hash_rev';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'files',
        'chains',
        'owner'
    ];

    /**
     * HashRepository constructor
     *
     * @param HashCommit $model
     */
    public function __construct(HashCommit $model)
    {
        $this->model = $model;
    }

    /**
     * Delete record
     *
     * @param type $id
     */
    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $this->model = $this->find($id);
            $this->model->files()->delete();
            $this->model->chains()->delete();
            $this->model->delete();
        });
    }

    /**
     * Save hash and it's relations
     *
     * @param array $data
     * @return model
     */
    protected function save($data)
    {
        $this->model->fill($data);

        DB::transaction(function () use ($data) {
            $this->model->saveOrFail();

            //$parser = new DescriptionParser($this->model->commit_description);

            //var_dump($parser);

            // save hash files
            $this->saveFiles($data['files'] ?? []);

            // save hash chains
            $this->saveChains($data['chains'] ?? []);
        });

        $this->model->load($this->with);

        return $this->model;
    }

    /**
     * Save hash files
     *
     * @param array $files
     */
    protected function saveFiles($files)
    {
        // delete old files before setting new ones
        $this->model->files()->delete();

        $this->model->files()->createMany(
            array_map(function ($file_name) {
                return ['file_name' => $file_name];
            }, $files)
        );
    }

    /**
     * Save hash chains
     *
     * @param array $chains
     */
    protected function saveChains($chains)
    {
        $chains = array_map(function ($chain_name) {
            return HashChain::where('chain_name', $chain_name)->first();
        }, $chains);

        $this->model->chains()->sync(array_column($chains, 'id'));
    }
}
