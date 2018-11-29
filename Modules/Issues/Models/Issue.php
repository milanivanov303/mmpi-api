<?php

namespace Modules\Issues\Models;

use Core\Models\Model;
use Modules\Projects\Models\Project;
use Modules\Instances\Models\Instance;

class Issue extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'project',
        'devInstance'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'project_id',
        'parent_issue_id',
        'dev_instance_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'revision',
        'subject',
        'tts_id',
        'jiraissue_id',
        'parent_issue_id',
        'dev_instance_id',
        'priority',
        'jira_admin_status',
        'created_on'
    ];

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
     * Get issue project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get issue dev instance
     */
    public function devInstance()
    {
        return $this->belongsTo(Instance::class, 'dev_instance_id');
    }

    /**
     * Get parent issue
     */
    public function parentIssue()
    {
        return $this->belongsTo(Issue::class, 'parent_issue_id');
    }
}
