<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = [
        'country', 'city', 'flag_emoji', 'address', 'phone', 'email',
        'type', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'headquarters' => 'Siège Social',
            'regional'     => 'Bureau Régional',
            default        => 'Représentation',
        };
    }
}
