<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $reason_id
 * @property string $reason_name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientProgram> $clientProgram
 * @property-read int|null $client_program_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SchoolProgram> $school_program
 * @property-read int|null $school_program_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason whereReasonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reason whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Reason extends Model
{
    use HasFactory;

    protected $table = 'tbl_reason';

    protected $primaryKey = 'reason_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reason_id',
        'reason_name',
        'type',
        'created_at',
        'updated_at',
    ];

    public function school_program()
    {
        return $this->hasMany(SchoolProgram::class, 'reason_id', 'reason_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'reason_id', 'reason_id');
    }
}
