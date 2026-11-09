<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CandidateProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'headline',
        'location',
        'career_objective',
        'resume_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills');
    }
}
