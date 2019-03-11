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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'project'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash',
        'organization_name',
        'valid_from',
        'valid_to'
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'project_id'
    ];

    /**
     * Define filters for this model
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            'project' => function ($builder, $value, $operator) {
                return $builder->whereHas('project', function ($query) use ($value, $operator) {
                    $query->where('name', $operator, $value);
                });
            },
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
                return $model->select("{$this->table}.*")
                        ->join('projects', 'projects.id', '=', "{$this->table}.project_id")
                        ->orderBy('projects.name', $order_dir);
            },
        ];
    }

    /**
     * Get owner
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
