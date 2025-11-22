<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormationView extends Model
{
    use HasFactory;

    protected $fillable = [
        'formation_id',
        'ip_address',
        'viewed_date',
    ];

    protected $casts = [
        'viewed_date' => 'date',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}
