<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property float $top
 * @property float $left
 * @property float $scaleX
 * @property float $scaleY
 * @property float $angle
 * @property int $flipX 0: False, 1: True
 * @property int $flipY 0: False, 1: True
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereAngle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereFlipX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereFlipY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereLeft($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereScaleX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereScaleY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereTop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Axis whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Axis extends Model
{
    use HasFactory;

    protected $table = 'tbl_axis';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'top',
        'left',
        'scaleX',
        'scaleY',
        'angle',
        'flipX',
        'flipY',
        'type',
    ];
}
