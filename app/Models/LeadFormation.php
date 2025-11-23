<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFormation extends Model
{
    use HasFactory;

    protected $table = 'lead_formation';

    protected $fillable = [
        'lead_id',
        'formation_id',
        'attentes',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}
