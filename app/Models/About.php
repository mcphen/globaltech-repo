<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = ['content', 'image_path'];

    public function getImagePathAttribute($value)
    {
        // In production, prepend sophieWeeding/public/storage to the path
        if (app()->environment('production') && $value && !str_starts_with($value, '/globaltech/public/storage/')) {
            return '/globaltech/public/storage/' . ltrim($value, '/');
        }

        return "/storage/".$value;
    }
}
