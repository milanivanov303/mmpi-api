<?php

namespace App\Modules\PatchRequests\Models;

use App\Models\Model;

class PatchRequest extends Model
{
    /**
     * Array with mapped attributes for conversion
     *
     * @var array
     */
    protected $mapping = [

    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    /**
     * Define filters for this model
     *
     * @return array
     */
    public function filters(): array
    {
        return [

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
     * Get attached modifications
     */
    public function modifications()
    {
        return $this->belongsToMany(Modification::class, 'modif_to_pr', 'pr_id', 'modif_id')->withPivot('removed');
    }

    /**
     * Get attached modifications
     */
    public function patch()
    {
        return $this->hasOne(Patch::class)->with('project');
    }
}
