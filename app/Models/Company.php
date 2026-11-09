<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'industry_id',
        'name',
        'slug',
        'website',
        'phone',
        'organization_type',
        'description',
        'is_verified',
    ];

    protected function casts(): array
    {
        return ['is_verified' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}
