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

    public const DEFAULT_OFFICES = [
        ['country' => "Côte d'Ivoire", 'city' => 'Abidjan', 'flag' => '🇨🇮', 'role' => 'Siège',           'address' => 'Plateau, Abidjan'],
        ['country' => 'Sénégal',       'city' => 'Dakar',   'flag' => '🇸🇳', 'role' => 'Bureau régional', 'address' => 'Plateau, Dakar'],
        ['country' => 'Mali',          'city' => 'Bamako',  'flag' => '🇲🇱', 'role' => 'Bureau régional', 'address' => 'ACI 2000, Bamako'],
        ['country' => 'Cameroun',      'city' => 'Douala',  'flag' => '🇨🇲', 'role' => 'Bureau régional', 'address' => 'Bonanjo, Douala'],
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
