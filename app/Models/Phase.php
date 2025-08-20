<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $phase_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PhaseDetail> $phase_details
 * @property-read int|null $phase_details_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase wherePhaseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phase whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Phase extends Model
{
    use HasFactory;

    protected $table = 'phases';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'phase_name',
    ];

    public function phase_details()
    {
        return $this->hasMany(PhaseDetail::class, 'phase_id', 'id');
    }
}
