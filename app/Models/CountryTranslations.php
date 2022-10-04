<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryTranslations extends Model
{
    use HasFactory;

    protected $table = 'lc_countries_translations';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug', 
        'locale',
    ];

    public function has_country()
    {
        return $this->belongsTo(Country::class, 'lc_country_id', 'id');
    }
}
