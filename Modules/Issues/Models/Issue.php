<?php

namespace Modules\Issues\Models;

use Core\Models\Model;
use Modules\Projects\Models\Project;
use Modules\Instances\Models\Instance;

class Issue extends Model
{
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
     * Get issue project
     */
    protected function project()
    {
        return $this->belongsTo(Project::class)->minimal();
    }

    /**
     * Get issue dev instance
     */
    protected function devInstance()
    {
        return $this->belongsTo(Instance::class, 'dev_instance_id')->minimal();
    }

    /**
     * Get parent issue
     */
    protected function parentIssue()
    {
        return $this->belongsTo(Issue::class, 'parent_issue_id');
    }
}
