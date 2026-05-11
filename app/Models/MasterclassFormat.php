<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterclassFormat extends Model
{
    protected $table = 'masterclass_formats';

    protected $fillable = [
        'icon', 'titre', 'couleur', 'description',
        'tags', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'tags'      => 'array',
        'is_active' => 'boolean',
    ];
}
