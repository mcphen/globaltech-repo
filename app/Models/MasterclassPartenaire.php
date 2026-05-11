<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterclassPartenaire extends Model
{
    protected $table = 'masterclass_partenaires';

    protected $fillable = [
        'nom', 'logo_icon', 'pays', 'description',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
