<?php

namespace App\Models;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $schdetail_id
 * @property string $sch_id
 * @property string|null $schdetail_fullname
 * @property string|null $schdetail_email
 * @property string|null $schdetail_grade
 * @property string|null $schdetail_position
 * @property string|null $schdetail_phone
 * @property int $is_pic
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read AgendaSpeaker|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $as_event_speaker
 * @property-read int|null $as_event_speaker_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SchoolProg> $as_schoolprog_speaker
 * @property-read int|null $as_schoolprog_speaker_count
 * @property-read SchoolDetail|null $pic_school_visit
 * @property-read \App\Models\School $school
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereIsPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereSchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereSchdetailEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereSchdetailFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereSchdetailGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereSchdetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereSchdetailPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereSchdetailPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolDetail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_schdetail';

    protected $primaryKey = 'schdetail_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'schdetail_fullname',
        'schdetail_email',
        'schdetail_grade',
        'schdetail_position',
        'schdetail_phone',
        'is_pic',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function as_event_speaker()
    {
        return $this->belongsToMany(Event::class, 'tbl_agenda_speaker', 'sch_pic_id', 'event_id')->using(AgendaSpeaker::class);
    }

    public function as_schoolprog_speaker()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_agenda_speaker', 'sch_pic_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }

    public function pic_school_visit()
    {
        return $this->belongsTo(SchoolDetail::class, 'school_pic', 'schdetail_id');
    }
}
