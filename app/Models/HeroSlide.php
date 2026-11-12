<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'image_path',
        'title',
        'subtitle',
        'text',
        'button1_label',
        'button1_url',
        'button2_label',
        'button2_url',
        'text_position',
        'overlay_opacity',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active'          => 'boolean',
        'overlay_opacity' => 'integer',
        'sort_order'      => 'integer',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? '/storage/' . $this->image_path : null;
    }
}
