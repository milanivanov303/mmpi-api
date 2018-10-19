<?php

namespace App\Modules\Issues\Models;

use App\Models\Model;
use App\Models\Project;
use App\Models\Instance;
use App\Traits\Filterable;

class Issue extends Model
{
    use Filterable;

    /**
     * Array with mapped attributes for conversion
     *
     * @var array
     */
    protected $mapping = [
        'project_id'      => 'project',
        'dev_instance_id' => 'dev_instance',
        'parent_issue_id' => 'parent_issue'
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
    protected function filters(): array
    {
        return [
            'subject' => function ($model, $value) {
                return $model->where('subject', 'like', "%{$value}%");
            },
            'project_id' => function ($model, $value) {
                return $model->whereHas('project', function ($query) use ($value) {
                    $query->where('name', '=', $value);
                });
            },
            'parent_issue_id' => function ($model, $value) {
                return $model->whereHas('parentIssue', function ($query) use ($value) {
                    $query->where('tts_id', '=', $value);
                });
            },
        ];
    }

    /**
     * Define order by for this model
     *
     * @return array
     */
    protected function orderBy(): array
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

    /**
     * Set project id attribute
     *
     * @param string $value
     */
    public function setProjectIdAttribute($value)
    {
        $project = Project::where('name', $value)->first();
        $this->attributes['project_id'] = $project->id;
    }

    /**
     * Set project id attribute
     *
     * @param string $value
     */
    public function setDevInstanceIdAttribute($value)
    {
        $instance = Instance::where('name', $value)->first();
        $this->attributes['dev_instance_id'] = $instance->id ?? null;
    }

    /**
     * Set project id attribute
     *
     * @param string $value
     */
    public function setParentIssueIdAttribute($value)
    {
        $issue = Issue::where('tts_id', $value)->first();
        $this->attributes['parent_issue_id'] = $issue->id ?? null;
    }

    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function relationsToArray()
    {
        $array = parent::relationsToArray();

        if ($this->isVisible('jiraissue_id') && array_key_exists('jiraissue_id', $array)) {
            $array['jiraissue_id'] = (int) $array['jiraissue_id'];
        }

        if ($this->isVisible('project') && array_key_exists('project', $array)) {
            $array['project_id'] = $array['project']['name'];
        }

        if ($this->isVisible('dev_instance') && array_key_exists('dev_instance', $array)) {
            $array['dev_instance_id'] = $array['dev_instance']['name'];
        }

        if ($this->isVisible('parent_issue') && array_key_exists('parent_issue', $array)) {
            $array['parent_issue_id'] = $array['parent_issue']['tts_id'];
        }

        return $array;
    }
}
