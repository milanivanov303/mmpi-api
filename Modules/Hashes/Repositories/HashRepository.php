<?php

namespace Modules\Hashes\Repositories;

use Modules\Hashes\Jobs\ProcessTags;
use Modules\Hashes\Models\HashCommit;
use Modules\Hashes\Models\HashChain;
use Core\Repositories\RepositoryInterface;
use Core\Repositories\AbstractRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

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
     * Define filters for this model
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            'committed_by' => function ($model, $value, $operator) {
                return $model->whereHas('owner', function ($query) use ($value, $operator) {
                    $query->where('username', $operator, $value);
                });
            },
            'files' => function ($model, $value) {
                return $model->whereHas('files', function ($query) use ($value) {
                    $query->where('file_name', 'like', "%{$value}%");
                });
            },
            'chains' => function ($model, $value) {
                return $model->whereHas('chains', function ($query) use ($value) {
                    $query->whereHas('chain', function ($query) use ($value) {
                        $query->where('chain_name', 'like', "%{$value}%");
                    });
                });
            }
        ];
    }

    /**
     * Define order by for this model
     *
     * @return array
     */
    public function orderBy(): array
    {
        return [

        ];
    }

    /**
     * Delete record
     *
     * @param mixed $id
     */
    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $this->model = $this->find($id);

            $this->model->files()->delete();
            $this->model->chains()->sync([]);

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

            // save hash files
            $this->saveFiles($data['files'] ?? []);

            // save hash chains
            $this->saveChains($data['chains'] ?? []);

            // save tags from description
            $this->saveTags();
        });

        $this->model->load($this->with);

        return $this->model;
    }

    /**
     * Save tags
     */
    protected function saveTags()
    {
        dispatch(
            (new ProcessTags($this->model))
                ->onQueue('tags')
        );
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
