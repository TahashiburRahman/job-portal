<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = ['job_listing_id', 'user_id', 'cover_letter', 'status'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_listing_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
