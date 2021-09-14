<?php

namespace Modules\PatchRequestSpecifications\Models;

use Core\Models\Model;
use App\Models\User;
use Modules\PatchRequests\Models\PatchRequest;

class PatchRequestsSpecification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patch_request_id',
        'user_id',
        'specification',
        'made_on',
        'made_by'
    ];

    /**
     * Get patch request
     */
    protected function patchRequests()
    {
        return $this->belongsTo(PatchRequest::class, 'id');
    }

    /**
     * Get user
     */
    protected function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get users
     */
    protected function madeBy()
    {
        return $this->belongsTo(User::class, 'made_by');
    }
}
