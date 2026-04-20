<?php

namespace App\Models;

use App\Trait\FileConfigTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Formation extends Model
{
    use HasFactory, FileConfigTrait;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'attachment_path',
        'category_id',
        'certification_type',
        'level',
        'is_featured',
        'language',
        'date_mode',
        'date',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'duration_hours',
        'duration_per_day_hours',
        'price',
        'currency',
    ];

    public function getImagePathAttribute($pics){
        return $this->picsEnv($pics);
    }

    public function getAttachmentPathAttribute($pics){
        return $this->picsEnv($pics);
    }

    protected $casts = [
        'date'        => 'date',
        'start_date'  => 'date',
        'end_date'    => 'date',
        'start_time'  => 'string',
        'end_time'    => 'string',
        'price'       => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (self $formation) {
            // UUID
            if (empty($formation->uuid)) {
                $formation->uuid = (string) Str::uuid();
            }
            // Slug from title, ensure uniqueness
            if (!empty($formation->title) && empty($formation->slug)) {
                $base = Str::slug($formation->title);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.(++$i);
                }
                $formation->slug = $slug;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(FormationCategory::class, 'category_id');
    }

    public function views()
    {
        return $this->hasMany(FormationView::class);
    }

    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'lead_formation', 'formation_id', 'lead_id')
            ->withPivot('attentes', 'status', 'paid_at')
            ->withTimestamps();
    }

    public function b2bRequests()
    {
        return $this->hasMany(FormationB2bRequest::class);
    }
}
