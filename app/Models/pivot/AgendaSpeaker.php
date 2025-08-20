<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string|null $event_id
 * @property int|null $sch_prog_id
 * @property int|null $partner_prog_id
 * @property int|null $eduf_id
 * @property int|null $sch_pic_id
 * @property int|null $univ_pic_id
 * @property int|null $partner_pic_id
 * @property string|null $empl_id ALL-In PIC
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int $priority
 * @property int $status
 * @property string $speaker_type
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereEdufId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereEmplId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker wherePartnerPicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker wherePartnerProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereSchPicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereSchProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereSpeakerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereUnivPicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgendaSpeaker whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AgendaSpeaker extends Pivot
{
    protected $table = 'tbl_agenda_speaker';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'event_id',
        'sch_prog_id',
        'partner_prog_id',
        'eduf_id',
        'sch_pic_id',
        'univ_pic_id',
        'partner_pic_id',
        'empl_id',
        'start_time',
        'end_time',
        'priority',
        'status',
        'speaker_type',
        'notes',
    ];
}
