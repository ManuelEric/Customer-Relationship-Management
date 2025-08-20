<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $prog_id
 * @property int|null $main_prog_id
 * @property int|null $sub_prog_id
 * @property string|null $prog_type
 * @property string $prog_mentor
 * @property string $prog_payment
 * @property string|null $prog_scope
 * @property string|null $prog_program
 * @property string|null $main_prog_name
 * @property string|null $sub_prog_name
 * @property int $active
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $program_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SeasonalProgram> $seasonalProgram
 * @property-read int|null $seasonal_program_count
 * @property-read \App\Models\SubProg|null $sub_prog
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereMainProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereMainProgName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereProgMentor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereProgPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereProgProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereProgScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereProgType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereProgramName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereSubProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram whereSubProgName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewProgram withAndWhereHas($relation, $constraint)
 *
 * @mixin \Eloquent
 */
class ViewProgram extends Model
{
    use HasFactory;

    protected $table = 'program';
    // protected $primaryKey = 'prog_id';

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    public function sub_prog()
    {
        return $this->belongsTo(SubProg::class, 'sub_prog_id', 'id');
    }

    public function seasonalProgram()
    {
        return $this->hasMany(SeasonalProgram::class, 'prog_id', 'prog_id');
    }
}
