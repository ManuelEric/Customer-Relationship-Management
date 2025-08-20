<?php

namespace App\Models\pivot;

use App\Models\User;
use App\Observers\ClientMentorObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[ObservedBy([ClientMentorObserver::class])]
/**
 * @property int $id
 * @property int $clientprog_id
 * @property string $user_id
 * @property string|null $timesheet_link
 * @property int $type 1: Supervising Mentor, 2: Profile Building & Exploration Mentor, 3: Aplication Strategy Mentor, 4: Writing Mentor, 5: Tutor, 6: Subject Specialist
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor whereClientprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor whereTimesheetLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientMentor whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ClientMentor extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_client_mentor';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'clientprog_id',
        'user_id',
        'timesheet_link',
        'type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
