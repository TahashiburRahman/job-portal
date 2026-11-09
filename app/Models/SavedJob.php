<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedJob extends Model
{
    protected $fillable = ['job_listing_id', 'user_id'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_listing_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
