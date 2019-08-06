<?php

namespace Modules\Issues\Repositories;

use Core\Repositories\RepositoryInterface;
use Core\Repositories\AbstractRepository;
use Modules\Instances\Models\Instance;
use Modules\Issues\Models\Issue;
use Modules\Projects\Models\Project;

class IssueRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * Custom unique primaryKey
     *
     * @var array
     */
    protected $customUniqueKey = 'tts_id';

    /**
     * HashRepository constructor
     *
     * @param Issue $model
     */
    public function __construct(Issue $model)
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
            'subject' => function ($builder, $value) {
                if (strpos($value, '%') === false) {
                    $value = "{$value}%";
                }
                return $builder->where('subject', 'like', $value);
            },
            'project' => function ($builder, $value, $operator) {
                return $builder->whereHas('project', function ($query) use ($value, $operator) {
                    $query->where('name', $operator, $value);
                });
            },
            'parent_issue' => function ($builder, $value, $operator) {
                return $builder->whereHas('parentIssue', function ($query) use ($value, $operator) {
                    $query->where('tts_id', $operator, $value);
                });
            },
            'createdOnFrom' => function ($builder, $value) {
                return $builder->whereRaw("DATE(created_on) >= ?", $value);
            },
            'createdOnTo' => function ($builder, $value) {
                return $builder->whereRaw("DATE(created_on) <= ?", $value);
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
            'project' => function ($model, $order_dir) {
                return $model->select('issues.*')->join('projects', 'projects.id', '=', 'issues.project_id')
                    ->orderBy('projects.name', $order_dir);
            },
            'parent_issue' => function ($model, $order_dir) {
                return $model->select('issues.*')->join('issues AS parent', 'parent.id', '=', 'issues.parent_issue_id')
                    ->orderBy('parent.tts_id', $order_dir);
            }
        ];
    }

    /**
     * Fill model attributes
     *
     * @param array $data
     */
    protected function fillModel(array $data)
    {
        parent::fillModel($data);

        if (array_key_exists('project', $data)) {
            $this->model->project()->associate(
                app(Project::class)->getModelId($data['project'], 'name')
            );
        }

        if (array_key_exists('dev_instance', $data)) {
            $this->model->devInstance()->associate(
                $data['dev_instance']["id"] ?? null
            );
        }

        if (array_key_exists('parent_issue', $data)) {
            $this->model->parentIssue()->associate(
                app(Issue::class)->getModelId($data['parent_issue'], ['tts_id', 'jiraissue_id'])
            );
        }
    }
}
