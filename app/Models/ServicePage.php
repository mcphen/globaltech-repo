<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePage extends Model
{
    protected $fillable = [
        'hero_badge', 'hero_title', 'hero_gradient_word', 'hero_subtitle', 'hero_stats',
        'services_label', 'services_title',
        'consulting_label', 'consulting_title', 'consulting_subtitle', 'consulting_offers',
        'process_label', 'process_title', 'process_steps',
        'cta_title', 'cta_subtitle',
    ];

    protected $casts = [
        'hero_stats'        => 'array',
        'consulting_offers' => 'array',
        'process_steps'     => 'array',
    ];
}
