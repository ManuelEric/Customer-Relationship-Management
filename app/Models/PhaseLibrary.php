<?php

namespace App\Models;

use App\Models\pivot\ClientProgramDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $phase_detail_id
 * @property string $nation
 * @property int $grade
 * @property string $quota
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ClientProgramDetail|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientProgram> $client_program
 * @property-read int|null $client_program_count
 * @property-read \App\Models\PhaseDetail $phase_detail
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary whereNation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary wherePhaseDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary whereQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseLibrary whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PhaseLibrary extends Model
{
    use HasFactory;

    protected $table = 'phase_libraries';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nation',
        'grade',
        'quota',
    ];

    public function phase_detail()
    {
        return $this->belongsTo(PhaseDetail::class, 'phase_detail_id', 'id');
    }

    public function client_program()
    {
        return $this->belongsToMany(ClientProgram::class, 'client_program_details', 'phase_lib_id', 'clientprog_id')->using(ClientProgramDetail::class)->withPivot('quota');
    }
}
