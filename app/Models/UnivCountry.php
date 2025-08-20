<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $tag
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tag $tags
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\University> $universities
 * @property-read int|null $universities_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnivCountry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnivCountry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnivCountry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnivCountry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnivCountry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnivCountry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnivCountry whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UnivCountry whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UnivCountry extends Model
{
    use HasFactory;

    protected $table = 'tbl_country';

    protected $fillable = [
        'name',
        'tag',
    ];

    /**
     * The relations.
     */
    public function universities()
    {
        return $this->hasMany(University::class, 'univ_country', 'id');
    }

    public function tags()
    {
        return $this->belongsTo(Tag::class, 'tag', 'id');
    }
}
