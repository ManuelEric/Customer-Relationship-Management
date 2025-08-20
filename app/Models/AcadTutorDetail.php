<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $clientprog_id
 * @property string $date
 * @property string $time
 * @property string $link online meeting room
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClientProgram $clientProgram
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail whereClientprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcadTutorDetail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AcadTutorDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_acad_tutor_dtl';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'clientprog_id',
        'date',
        'time',
        'link',
    ];

    public function clientProgram()
    {
        return $this->belongsTo(ClientProgram::class, 'clientprog_id', 'clientprog_id');
    }
}
