<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $sch_id
 * @property string $prog_id
 * @property string|null $first_discuss
 * @property int $status 0: Pending, 1: Success, 2: Rejected 3: Refund 4: Accepted 5: Cancel
 * @property string|null $notes
 * @property string|null $notes_detail
 * @property string|null $refund_notes
 * @property string|null $refund_date
 * @property string|null $running_status
 * @property int|null $total_hours
 * @property float|null $total_fee
 * @property int|null $participants
 * @property string|null $place
 * @property string|null $end_program_date
 * @property string|null $start_program_date
 * @property string|null $success_date
 * @property string|null $cancel_date
 * @property string|null $accepted_date
 * @property string|null $pending_date
 * @property int|null $reason_id
 * @property string|null $reason_notes
 * @property string|null $denied_date
 * @property string|null $empl_id ALL-In PIC
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read AgendaSpeaker|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $internal_speaker
 * @property-read int|null $internal_speaker_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CorporatePic> $partner_speaker
 * @property-read int|null $partner_speaker_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SchoolDetail> $pic_speaker
 * @property-read int|null $pic_speaker_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereAcceptedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereCancelDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereDeniedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereEmplId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereEndProgramDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereFirstDiscuss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereNotesDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg wherePendingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg wherePlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereReasonNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereRefundDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereRefundNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereRunningStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereSchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereStartProgramDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereSuccessDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereTotalFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereTotalHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolProg whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class SchoolProg extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sch_id',
        'prog_id',
        'first_discuss',
        'last_discuss',
        'status',
        'notes',
        'empl_id',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_school_program', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_school_program', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_school_program', 'channel_datatable'));

        return $model;
    }

    public function pic_speaker()
    {
        return $this->belongsToMany(SchoolDetail::class, 'tbl_agenda_speaker', 'sch_pic_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }

    public function partner_speaker()
    {
        return $this->belongsToMany(CorporatePic::class, 'tbl_agenda_speaker', 'partner_pic_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }

    public function internal_speaker()
    {
        return $this->belongsToMany(User::class, 'tbl_agenda_speaker', 'empl_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }
}
