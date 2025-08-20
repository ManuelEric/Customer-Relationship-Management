<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $sch_id
 * @property string $internal_pic
 * @property int $school_pic
 * @property string $visit_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property-read \App\Models\User $pic_from_allin
 * @property-read \App\Models\SchoolDetail $pic_from_school
 * @property-read \App\Models\School $school
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit whereInternalPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit whereSchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit whereSchoolPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolVisit whereVisitDate($value)
 *
 * @mixin \Eloquent
 */
class SchoolVisit extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch_visit';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sch_id',
        'internal_pic',
        'school_pic',
        'visit_date',
        'notes',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function pic_from_allin()
    {
        return $this->belongsTo(User::class, 'internal_pic', 'id');
    }

    public function pic_from_school()
    {
        return $this->belongsTo(SchoolDetail::class, 'school_pic', 'schdetail_id');
    }
}
