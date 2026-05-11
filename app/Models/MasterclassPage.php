<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterclassPage extends Model
{
    protected $table = 'masterclass_page';

    protected $fillable = [
        'hero_badge',
        'hero_title',
        'hero_subtitle',
        'hero_description',
        'section_titre',
        'section_description',
        'approche',
        'langues',
        'cohortes',
        'duree_info',
        'financement_info',
    ];
}
