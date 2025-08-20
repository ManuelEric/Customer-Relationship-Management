<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $stream_name
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder<static>|Stream active()
 * @method static Builder<static>|Stream newModelQuery()
 * @method static Builder<static>|Stream newQuery()
 * @method static Builder<static>|Stream query()
 * @method static Builder<static>|Stream whereCreatedAt($value)
 * @method static Builder<static>|Stream whereId($value)
 * @method static Builder<static>|Stream whereIsActive($value)
 * @method static Builder<static>|Stream whereStreamName($value)
 * @method static Builder<static>|Stream whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Stream extends Model
{
    use HasFactory;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'stream_name',
        'is_active',
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

    public function scopeActive(Builder $query)
    {
        $query->where('is_active', 1);
    }
}
