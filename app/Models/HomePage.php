<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    protected $fillable = [
        'hero_badge', 'hero_title_1', 'hero_title_2', 'hero_title_3', 'hero_subtitle',
        'hero_trust_badges', 'hero_mini_stats',
        'stats',
        'pillars_label', 'pillars_title', 'pillars_subtitle',
        'b2b_badge', 'b2b_title_1', 'b2b_title_2', 'b2b_subtitle',
        'b2b_list_items', 'b2b_benefits',
        'why_label', 'why_title', 'why_subtitle',
        'portfolio_label', 'portfolio_title',
        'testimonials_label', 'testimonials_title',
        'news_label', 'news_title',
        'cta_badge', 'cta_title', 'cta_gradient_word', 'cta_subtitle',
    ];

    protected $casts = [
        'hero_trust_badges' => 'array',
        'hero_mini_stats'   => 'array',
        'stats'             => 'array',
        'b2b_list_items'    => 'array',
        'b2b_benefits'      => 'array',
    ];
}
