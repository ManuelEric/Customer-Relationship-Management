<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $lc_country_id
 * @property string $name
 * @property string $slug
 * @property string $locale
 * @property-read \App\Models\Country $has_country
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryTranslations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryTranslations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryTranslations query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryTranslations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryTranslations whereLcCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryTranslations whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryTranslations whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CountryTranslations whereSlug($value)
 *
 * @mixin \Eloquent
 */
class CountryTranslations extends Model
{
    use HasFactory;

    protected $table = 'lc_countries_translations';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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
