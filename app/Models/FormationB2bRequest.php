<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormationB2bRequest extends Model
{
    protected $fillable = [
        'formation_id',
        'company_name',
        'company_sector',
        'contact_first_name',
        'contact_last_name',
        'contact_function',
        'contact_email',
        'contact_phone',
        'participants',
        'attentes',
        'status',
    ];

    protected $casts = [
        'participants' => 'array',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}
