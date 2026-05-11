<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterclassDomaine extends Model
{
    protected $table = 'masterclass_domaines';

    protected $fillable = [
        'icon', 'color', 'titre', 'description',
        'niveau', 'duree', 'modules', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'modules'   => 'array',
        'is_active' => 'boolean',
    ];
}
