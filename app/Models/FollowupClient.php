<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FollowupClient
 *
 * @property int $id
 * @property string|null $user_id
 * @property string|null $client_id
 * @property \App\Models\User|null $pic
 * @property \App\Models\Client|null $client
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUpSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUpSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowUpSchedule query()
 *
 * @property string $followup_date
 * @property string|null $notes
 * @property string|null $minutes_of_meeting
 * @property int $status 0: Not yet, 1: Done, 2: Pause, 3: Negotiation
 * @property int $reminder_is_sent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereFollowupDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereMinutesOfMeeting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereReminderIsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowupClient whereUserId($value)
 *
 * @mixin \Eloquent
 */
class FollowupClient extends Model
{
    use HasFactory;

    protected $table = 'tbl_followup_client';

    protected $fillable = [
        'client_id',
        'user_id',
        'notes',
        'minutes_of_meeting',
        'status',
        'reminder_is_sent',
        'followup_date',
    ];

    /**
     * Get the client associated with this follow-up.
     */
    public function client()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }

    /**
     * Get the PIC (Person In Charge) user for this follow-up schedule.
     */
    public function pic()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
