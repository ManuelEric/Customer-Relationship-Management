<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $lc_region_id
 * @property string $uuid
 * @property string $official_name
 * @property string $iso_alpha_2
 * @property string $iso_alpha_3
 * @property int|null $iso_numeric
 * @property string|null $geoname_id
 * @property string|null $international_phone
 * @property string|null $languages
 * @property string|null $tld Top-level domain
 * @property string|null $wmo Country abbreviations by the World Meteorological Organization
 * @property string $emoji
 * @property string $color_hex
 * @property string $color_rgb
 * @property string|null $coordinates
 * @property string|null $coordinates_limit
 * @property int $visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CountryTranslations> $has_translations
 * @property-read int|null $has_translations_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereColorHex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereColorRgb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCoordinatesLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereEmoji($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereGeonameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereInternationalPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoAlpha2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoAlpha3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereIsoNumeric($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereLanguages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereLcRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereOfficialName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereTld($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country whereWmo($value)
 *
 * @mixin \Eloquent
 */
class Country extends Model
{
    use HasFactory;

    protected $table = 'lc_countries';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lc_region_id',
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
        'visible',
    ];

    public function has_translations()
    {
        return $this->hasMany(CountryTranslations::class, 'lc_country_id', 'id');
    }
}
