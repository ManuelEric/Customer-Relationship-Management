<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $lc_region_id
 * @property string $name
 * @property string $slug
 * @property string $locale
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionTranslations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionTranslations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionTranslations query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionTranslations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionTranslations whereLcRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionTranslations whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionTranslations whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegionTranslations whereSlug($value)
 *
 * @mixin \Eloquent
 */
class RegionTranslations extends Model
{
    use HasFactory;

    protected $table = 'lc_region_translations';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lc_region_id',
        'name',
        'slug',
        'locale',
    ];
}
