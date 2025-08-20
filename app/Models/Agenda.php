<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $agenda_id
 * @property int|null $sch_prog_id
 * @property int|null $partner_prog_id
 * @property int|null $eduf_id
 * @property string|null $event_id
 * @property string|null $event_title
 * @property string|null $event_description
 * @property string|null $event_startdate
 * @property string|null $event_enddate
 * @property string|null $partner_pic_name
 * @property string|null $partner_pic_phone
 * @property string|null $corp_name
 * @property string|null $school_pic_name
 * @property string|null $school_pic_phone
 * @property string|null $school_id
 * @property string|null $school_name
 * @property int|null $sch_pic_id
 * @property int|null $univ_pic_id
 * @property int|null $partner_pic_id
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int $priority
 * @property int $status
 * @property string $speaker_type
 * @property string|null $university_pic_name
 * @property string|null $university_pic_phone
 * @property string|null $university_name
 * @property string|null $internal_pic
 * @property string|null $school_program_name
 * @property string|null $school_main_program
 * @property string|null $school_sub_program
 * @property string|null $partner_program_name
 * @property string|null $partner_main_program
 * @property string|null $partner_sub_program
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereAgendaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereCorpName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereEdufId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereEventDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereEventEnddate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereEventStartdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereEventTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereInternalPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda wherePartnerMainProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda wherePartnerPicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda wherePartnerPicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda wherePartnerPicPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda wherePartnerProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda wherePartnerProgramName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda wherePartnerSubProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSchPicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSchProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSchoolMainProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSchoolName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSchoolPicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSchoolPicPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSchoolProgramName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSchoolSubProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereSpeakerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereUnivPicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereUniversityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereUniversityPicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agenda whereUniversityPicPhone($value)
 *
 * @mixin \Eloquent
 */
class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';
}
