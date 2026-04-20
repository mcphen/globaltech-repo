<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormationCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'color', 'background',
        'description', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function formations(): HasMany
    {
        return $this->hasMany(Formation::class, 'category_id');
    }
}
