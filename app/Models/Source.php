<?php

namespace App\Models;

class Source extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'source';

    public function revisions()
    {
        return $this->hasMany(SourceRevision::class, 'source_id', 'source_id');
    }

    public function scopeWithRevision($query, $revision)
    {
        return $query->whereHas('revisions', function ($query) use ($revision) {
            $query->where('revision', $revision);
        });
    }
}
