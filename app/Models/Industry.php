<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    protected $fillable = ['name', 'slug'];

    public function subcategories(): HasMany
    {
        return $this->hasMany(IndustrySubcategory::class);
    }
}
