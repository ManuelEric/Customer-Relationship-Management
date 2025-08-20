<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $category
 * @property int $max_score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScoringParam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScoringParam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScoringParam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScoringParam whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScoringParam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScoringParam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScoringParam whereMaxScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScoringParam whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ScoringParam extends Model
{
    use HasFactory;

    protected $table = 'tbl_scoring_param';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category',
        'max_score',
    ];
}
