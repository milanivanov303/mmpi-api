<?php

namespace Modules\Hashes\Repositories;

use App\Models\EnumValue;
use App\Models\User;
use Modules\Branches\Models\Branch;
use Modules\Hashes\Jobs\ProcessTags;
use Modules\Hashes\Models\HashCommit;
use Core\Repositories\RepositoryInterface;
use Core\Repositories\AbstractRepository;
use Illuminate\Support\Facades\DB;

class HashRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Custom unique primaryKey
     *
     * @var string
     */
    protected $customUniqueKey = 'hash_rev';

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
                return $model->whereHas('committedBy', function ($query) use ($value, $operator) {
                    $query->where('username', $operator, $value);
                });
            },
            'files' => function ($model, $value) {
                return $model->whereHas('files', function ($query) use ($value) {
                    $query->where('file_name', 'like', "%{$value}%");
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
     * @inheritDoc
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('repo_type', $data)) {
            $this->model->repoType()->associate(
                app(EnumValue::class)
                    ->getModelId($data['repo_type'], 'key', ['type' =>'repository_type'])
            );
            // reload relation so we have last data in case it is needed in branch relation
            $this->model->load('repoType');
        }

        if (array_key_exists('branch', $data)) {
            $this->model->branch()->associate(
                app(Branch::class)
                    ->getModelId($data['branch'], 'name', ['repo_type_id' => $this->model->repoType->id])
            );
            // unset repoType relation as it is returned in response when not needed
            unset($this->model->repoType);
        }

        if (array_key_exists('committed_by', $data)) {
            $this->model->committedBy()->associate(
                app(User::class)->getModelId($data['committed_by'], 'username')
            );
        }

        //$this->model->made_on = Carbon::now()->format('Y-m-d H:i:s');
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $model = $this->find($id);

            $model->files()->delete();

            $model->delete();
        });
    }

    /**
     * @inheritDoc
     */
    protected function save($data)
    {
        $this->fillModel($data);

        DB::transaction(function () use ($data) {
            $modelExists = $this->model->exists;

            $this->model->saveOrFail();

            // save hash files
            if (array_key_exists('files', $data)) {
                if ($modelExists) {
                    $this->model->files()->delete();
                }

                $this->model->files()->createMany($data['files']);
            }

            // save tags from description
            $this->saveTags();
        });

        $this->loadModelRelations($data);

        return $this->model;
    }

    /**
     * Save tags
     */
    protected function saveTags()
    {
        dispatch(
            (new ProcessTags($this->model))->onQueue('hashes')
        );
    }
}
