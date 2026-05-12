<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Actualite extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'published_at',
        'category',
    ];

    public const CATEGORIES = [
        'actualite'   => 'Actualité',
        'projet'      => 'Projet',
        'partenariat' => 'Partenariat',
        'evenement'   => 'Événement',
    ];

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
