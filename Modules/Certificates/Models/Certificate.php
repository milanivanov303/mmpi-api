<?php

namespace Modules\Certificates\Models;

use Core\Models\Model;
use Modules\Projects\Models\Project;

class Certificate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'imx_certificates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'hash',
        'organization_name',
        'valid_from',
        'valid_to'
    ];

    /**
     * Get owner
     */
    protected function project()
    {
        return $this->belongsTo(Project::class);
    }
}
