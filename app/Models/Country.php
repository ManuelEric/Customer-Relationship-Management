<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'lc_countries';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'official_name',
        'iso_alpha_2', 
        'iso_alpha_3', 
        'iso_numeric',
        'geoname_id',
        'international_phone',
        'languages',
        'tld',
        'wmo',
        'emoji',
        'color_hex',
        'color_rgb',
        'coordinates',
        'coordinates_limit',
        'visible'
    ];

    public function has_translations()
    {
        return $this->hasMany(CountryTranslations::class, 'lc_country_id', 'id');
    }
}
