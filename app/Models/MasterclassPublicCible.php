<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterclassPublicCible extends Model
{
    protected $table = 'masterclass_public_cibles';

    protected $fillable = [
        'icon', 'titre', 'description',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
