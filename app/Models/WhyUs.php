<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyUs extends Model
{
    protected $table = 'why_us';

    protected $fillable = [
        'icon',
        'color',
        'title',
        'desc',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
