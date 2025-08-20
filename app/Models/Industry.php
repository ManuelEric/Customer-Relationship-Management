<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Industry whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Industry extends Model
{
    use HasFactory;

    protected $table = 'tbl_industry';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }
}
