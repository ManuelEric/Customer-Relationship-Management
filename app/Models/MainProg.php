<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $prog_name
 * @property string|null $group_of
 * @property int $prog_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Program> $program
 * @property-read int|null $program_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubProg> $subProgram
 * @property-read int|null $sub_program_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MainProg newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MainProg newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MainProg query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MainProg whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MainProg whereGroupOf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MainProg whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MainProg whereProgName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MainProg whereProgStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MainProg whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class MainProg extends Model
{
    use HasFactory;

    protected $table = 'tbl_main_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prog_name',
        'group_of',
        'prog_status',
    ];

    // relation
    public function subProgram()
    {
        return $this->hasMany(SubProg::class, 'main_prog_id', 'id');
    }

    public function program()
    {
        return $this->hasMany(Program::class, 'main_prog_id', 'id');
    }
}
