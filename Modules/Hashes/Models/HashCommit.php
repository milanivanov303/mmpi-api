<?php

namespace Modules\Hashes\Models;

use App\Models\EnumValue;
use Core\Models\Model;
use App\Models\User;

class HashCommit extends Model
{
    /**
     * Array with mapped attributes for conversion
     *
     * @var array
     */
    protected $mapping = [
        'commit_description' => 'description',
        'repo_branch'        => 'branch'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'module',
        'files',
        'committedBy'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "branch",
        "merge_branch",
        "hash_rev",
        "rev",
        "version",
        "description",
        "timestamp"
    ];

    /**
     * The attributes that will be hidden in output json
     *
     * @var array
     */
    protected $hidden = [
        'module_id'
    ];

    /**
     * Define filters for this model
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            'committed_by' => function ($model, $value, $operator) {
                return $model->whereHas('owner', function ($query) use ($value, $operator) {
                    $query->where('username', $operator, $value);
                });
            },
            'files' => function ($model, $value) {
                return $model->whereHas('files', function ($query) use ($value) {
                    $query->where('file_name', 'like', "%{$value}%");
                });
            },
            'chains' => function ($model, $value) {
                return $model->whereHas('chains', function ($query) use ($value) {
                    $query->whereHas('chain', function ($query) use ($value) {
                        $query->where('chain_name', 'like', "%{$value}%");
                    });
                });
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

        ];
    }

    /**
     * Get user for the hash.
     */
    public function module()
    {
        return $this->belongsTo(EnumValue::class, 'module_id');
    }

    /**
     * Get the files for the hash.
     */
    public function files()
    {
        return $this->hasMany(HashCommitFile::class, 'hash_commit_id');
    }

    /**
     * Get user for the hash.
     */
    public function committedBy()
    {
        return $this->belongsTo(User::class, 'committed_by');
    }

    /**
     * Set committed by attribute
     *
     * @param string $value
     */
    public function setCommittedByAttribute($value)
    {
        $user = User::where('username', $value)->first();
        $this->attributes['committed_by'] = $user->id;
    }
}
