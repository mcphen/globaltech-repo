<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterclassSession extends Model
{
    protected $table = 'masterclass_sessions';

    protected $fillable = [
        'badge_text', 'cta_titre', 'cta_description',
        'date_session', 'places_total', 'places_restantes',
        'prix_a_partir', 'financement', 'is_active',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'places_total'     => 'integer',
        'places_restantes' => 'integer',
    ];
}
