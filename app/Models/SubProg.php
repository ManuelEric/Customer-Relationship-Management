<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $main_prog_id
 * @property string $sub_prog_name
 * @property int $sub_prog_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MainProg $mainProgram
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SubProg> $program
 * @property-read int|null $program_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InitialProgram> $spesificConcern
 * @property-read int|null $spesific_concern_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg whereMainProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg whereSubProgName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg whereSubProgStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubProg withAndWhereHas($relation, $constraint)
 *
 * @mixin \Eloquent
 */
class SubProg extends Model
{
    use HasFactory;

    protected $table = 'tbl_sub_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sub_prog_name',
        'sub_prog_status',
    ];

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    // relation

    public function mainProgram()
    {
        return $this->belongsTo(MainProg::class, 'main_prog_id', 'id');
    }

    public function program()
    {
        return $this->hasMany(SubProg::class, 'sub_prog_id', 'id');
    }

    public function spesificConcern()
    {
        return $this->belongsToMany(InitialProgram::class, 'tbl_initial_prog_sub_lead', 'subprogram_id', 'initialprogram_id');
    }
}
