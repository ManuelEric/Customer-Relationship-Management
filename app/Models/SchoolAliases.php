<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $sch_id
 * @property string $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\School $school
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolAliases newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolAliases newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolAliases query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolAliases whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolAliases whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolAliases whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolAliases whereSchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolAliases whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolAliases extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch_aliases';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sch_id',
        'alias',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }
}
