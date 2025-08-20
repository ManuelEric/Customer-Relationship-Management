<?php

namespace App\Models;

use App\Models\pivot\ClientProgramDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $phase_id
 * @property string $phase_detail_name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ClientProgramDetail|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientProgram> $client_program
 * @property-read int|null $client_program_count
 * @property-read \App\Models\Phase $phase
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PhaseLibrary> $phase_libraries
 * @property-read int|null $phase_libraries_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseDetail wherePhaseDetailName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseDetail wherePhaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PhaseDetail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PhaseDetail extends Model
{
    use HasFactory;

    protected $table = 'phase_details';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'phase_detail_name',
        'type',
    ];

    public function phase()
    {
        return $this->belongsTo(Phase::class, 'phase_id', 'id');
    }

    public function phase_libraries()
    {
        return $this->hasMany(PhaseLibrary::class, 'phase_detail_id', 'id');
    }

    public function client_program()
    {
        return $this->belongsToMany(ClientProgram::class, 'client_program_details', 'phase_detail_id', 'clientprog_id')->using(ClientProgramDetail::class)->withPivot('quota', 'use');
    }
}
