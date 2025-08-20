<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $sch_id
 * @property string|null $corp_id
 * @property string|null $title
 * @property string|null $location
 * @property string|null $intr_pic
 * @property string|null $ext_pic_name
 * @property string|null $ext_pic_mail
 * @property string|null $ext_pic_phone
 * @property string|null $first_discussion_date
 * @property string|null $last_discussion_date
 * @property string|null $event_start
 * @property string|null $event_end
 * @property int $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
class EdufLead extends Model
{
    use HasFactory;

    protected $table = 'tbl_eduf_lead';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sch_id',
        'corp_id',
        'title',
        'location',
        'intr_pic',
        'ext_pic_name',
        'ext_pic_mail',
        'ext_pic_phone',
        'first_discussion_date',
        'last_discussion_date',
        'event_start',
        'event_end',
        'status',
        'notes',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_eduf_lead', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_eduf_lead', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_eduf_lead', 'channel_datatable'));

        return $model;
    }

    protected function organizerName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getOrganizerName()
        );
    }

    public function getOrganizerName()
    {
        $partnerName = $this->sch_id ? $this->schools->sch_name : ($this->corp_id ? $this->corps->partner_name : null);

        if ($partnerName) {
            $date = $this->event_start ?? $this->created_at;

            return $partnerName.' ('.date('d M Y', strtotime($date)).')';
        }

    }

    public function client()
    {
        return $this->hasMany(UserClient::class, 'eduf_id', 'id');
    }

    public function schools()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id')->withTrashed();
    }

    public function corps()
    {
        return $this->belongsTo(Corporate::class, 'corp_id', 'corp_id');
    }

    public function review()
    {
        return $this->hasMany(EdufReview::class, 'eduf_id', 'id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'eduf_lead_id', 'id');
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'eduf_id', 'id');
    }

    public function internal_speaker()
    {
        return $this->belongsToMany(User::class, 'tbl_agenda_speaker', 'eduf_id', 'id')->using(AgendaSpeaker::class);
    }
}
