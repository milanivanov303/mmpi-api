<?php

namespace App\Modules\Hashes\Repositories;

use App\Repositories\AbstractEloquentRepository;
use App\Modules\Hashes\Models\HashChain;
use Illuminate\Support\Facades\DB;

class EloquentHashRepository extends AbstractEloquentRepository implements HashRepository
{
    protected $primaryKey = 'hash_rev';

    public function create(array $data)
    {
        return $this->save($data);
    }

    public function update(array $data, $id)
    {
        $this->model = $this->model->where($this->primaryKey, $id)->firstOrFail();
        return $this->save($data);
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $this->model = $this->model->where($this->primaryKey, $id)->firstOrFail();
            $this->model->files()->delete();
            $this->model->chains()->delete();
            $this->model->delete();
        });
    }

    /**
     * Save hash and it's relations
     *
     * @param array $data
     */
    protected function save($data)
    {
        $this->model->fill($data);

        DB::transaction(function () use ($data) {
            $this->model->saveOrFail();

            // save hash files
            if (isset($data['files'])) {
                $this->saveFiles($data['files']);
            }

            // save hash chains
            if (isset($data['chains'])) {
                $this->saveChains($data['chains']);
            }
        });

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
        // delete old chains before setting new ones
        $this->model->chains()->delete();

        $this->model->chains()->createMany(
            array_map(function ($chain_name) {
                $chain = HashChain::where('chain_name', $chain_name)->firstOrFail();
                return ['hash_chain_id' => $chain->id];
            }, $chains)
        );
    }
}
