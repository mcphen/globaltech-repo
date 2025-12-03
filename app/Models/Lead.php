<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'company',
        'consent_at',
    ];

    protected $casts = [
        'consent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'lead_formation', 'lead_id', 'formation_id')
            ->withPivot('attentes', 'status', 'paid_at')
            ->withTimestamps();
    }

    public function appels(): MorphMany
    {
        return $this->morphMany(Appel::class, 'callable');
    }
}
