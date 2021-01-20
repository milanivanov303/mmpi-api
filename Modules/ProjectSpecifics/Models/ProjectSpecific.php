<?php

namespace Modules\ProjectSpecifics\Models;

use Core\Models\Model;
use App\Models\User;
use App\Models\EnumValue;
use Modules\Projects\Models\Project;

class ProjectSpecific extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prj_specific_feature_id',
        'project_id',
        'value',
        'date_characteristic',
        'made_by',
        'made_on',
        'comment'
    ];

    // protected $maps = [
    //     'value' => 'property'
    // ];

    // protected $append = ['property'];

    /**
     * Get project
     */
    protected function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get user
     */
    protected function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }

    /**
     * Get project specific features
     */
    protected function projectSpecificFeature()
    {
        return $this->belongsTo(EnumValue::class, 'prj_specific_feature_id');
    }

    // public function property()
    // {
    //     return $this->belongsTo(EnumValue::class, 'value');
    // }
}
