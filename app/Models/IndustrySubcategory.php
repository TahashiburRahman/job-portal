<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndustrySubcategory extends Model
{
    protected $fillable = ['industry_id', 'name', 'slug'];

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }
}
