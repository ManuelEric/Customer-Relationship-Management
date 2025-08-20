<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $clientprog_id
 * @property int|null $phase_detail_id
 * @property int|null $phase_lib_id
 * @property string $quota
 * @property float|null $use
 * @property int $grade
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail whereClientprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail wherePhaseDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail wherePhaseLibId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail whereQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramDetail whereUse($value)
 *
 * @mixin \Eloquent
 */
class ClientProgramDetail extends Pivot
{
    use HasFactory;

    protected $table = 'client_program_details';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'clientprog_id',
        'phase_detail_id',
        'phase_lib_id',
        'slot',
        'quota',
        'use',
        'grade',
        'nation',
    ];
}
