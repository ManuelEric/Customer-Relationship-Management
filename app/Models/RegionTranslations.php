<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionTranslations extends Model
{
    use HasFactory;

    protected $table = 'lc_region_translations';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'lc_region_id',
        'name', 
        'slug',
        'locale',
    ];
}
