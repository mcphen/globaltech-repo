<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterclassCertification extends Model
{
    protected $table = 'masterclass_certifications';

    protected $fillable = [
        'titre', 'description', 'badge_icon', 'couleur',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
