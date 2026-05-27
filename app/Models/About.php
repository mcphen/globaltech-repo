<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = [
        'content', 'image_path',
        'hero_badge', 'hero_title', 'hero_subtitle',
        'history_label', 'history_title', 'stats', 'values',
        'timeline_label', 'timeline_title', 'milestones',
        'offices_title', 'offices_subtitle', 'offices',
        'cta_title', 'cta_subtitle',
    ];

    protected $casts = [
        'stats'      => 'array',
        'values'     => 'array',
        'milestones' => 'array',
        'offices'    => 'array',
    ];

    public function getImagePathAttribute($value)
    {
        if (app()->environment('production') && $value && !str_starts_with($value, '/globaltech/public/storage/')) {
            return '/globaltech/public/storage/' . ltrim($value, '/');
        }
        return "/storage/" . $value;
    }
}
