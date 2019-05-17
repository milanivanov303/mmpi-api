<?php

namespace Modules\ProjectSpecifics\Models;

use Core\Models\Model;
use App\Models\User;
use App\Models\EnumValue;
use Modules\Projects\Models\Project;

class ProjectSpecific extends Model
{
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'madeBy',
        'projectSpecificFeature'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'prj_specific_feature_id',
        'project_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prj_specific_feature_id',
        'value',
        'date_characteristic',
        'made_by',
        'made_on',
        'comment'
    ];

    /**
     * Get project
     */
    public function project()
    {
        return $this->belongsTo(Project::class)->without('projectSpecifics');
    }

    /**
     * Get user
     */
    public function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by')->minimal();
    }

    /**
     * Get project specific features
     */
    public function projectSpecificFeature()
    {
        return $this->belongsTo(EnumValue::class, 'prj_specific_feature_id')->minimal();
    }
}
