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
 * @property-read \App\Models\Tag $tagCountry
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterCountry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterCountry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterCountry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterCountry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterCountry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterCountry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterCountry whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterCountry whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class MasterCountry extends Model
{
    use HasFactory;

    protected $table = 'tbl_country';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'tag',
    ];

    public function tagCountry()
    {
        return $this->belongsTo(Tag::class, 'tag', 'id');
    }
}
