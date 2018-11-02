<?php

namespace App\Modules\Issues\Models;

use App\Models\Model;
use App\Modules\Projects\Models\Project;
use App\Modules\Instances\Models\Instance;

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
            'project_id' => function ($builder, $value) {
                return $builder->whereHas('project', function ($query) use ($value) {
                    $query->where('name', '=', $value);
                });
            },
            'parent_issue_id' => function ($builder, $value) {
                return $builder->whereHas('parentIssue', function ($query) use ($value) {
                    $query->where('tts_id', '=', $value);
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
            'project_id' => function ($model, $order_dir) {
                return $model->select('issues.*')->join('projects', 'projects.id', '=', 'issues.project_id')
                             ->orderBy('projects.name', $order_dir);
            },
            'parent_issue_id' => function ($model, $order_dir) {
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
