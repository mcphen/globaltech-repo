<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appel extends Model
{

    use HasFactory;
    
    protected $fillable = [
        'callable_id',
        'callable_type',
        'user_id',
        'called_at',
        'type',
        'status',
        'duration',
        'notes',
        'next_call_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'next_call_at' => 'datetime',
    ];

    /**
     * Relation polymorphique
     */
    public function callable()
    {
        return $this->morphTo();
    }

    /**
     * L'utilisateur qui a effectué l'appel
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour les appels sortants
     */
    public function scopeSortant($query)
    {
        return $query->where('type', 'sortant');
    }

    /**
     * Scope pour les appels entrants
     */
    public function scopeEntrant($query)
    {
        return $query->where('type', 'entrant');
    }

    /**
     * Formater la durée en minutes:secondes
     */
    public function getDurationFormattedAttribute()
    {
        if (!$this->duration) return 'N/A';
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Vérifier si un rappel est prévu
     */
    public function getHasRappelAttribute()
    {
        return !is_null($this->next_call_at);
    }
}
