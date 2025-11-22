<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'subtitle',
        'description_old',
        'image_path',
        'min_price',
    ];

    // Accesseur pour URL de l'image
    public function getImageUrlAttribute()
    {
        return $this->image_path
            ? Storage::url($this->image_path)
            : null;
    }

    /**
     * The appointments that belong to the service.
     */
    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class);
    }

    /**
     * Get the items for the service.
     */
    public function items()
    {
        return $this->hasMany(ServiceItem::class);
    }
}
