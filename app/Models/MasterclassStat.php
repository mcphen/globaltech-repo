<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterclassStat extends Model
{
    protected $table = 'masterclass_stats';

    protected $fillable = ['valeur', 'label', 'sort_order'];
}
