<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $industry_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Industry $Industry
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubSector newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubSector newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubSector query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubSector whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubSector whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubSector whereIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubSector whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubSector whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SubSector extends Model
{
    use HasFactory;

    protected $table = 'tbl_industry_subsector';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'industry_id',
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

    public function Industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id', 'id');
    }
}
