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
     * Get parent issue
     */
    public function modifications()
    {
        return $this->morphToMany(Modification::class, 'modif_to_pr');
    }
}
