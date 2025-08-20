<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $event_id
 * @property string $event_title
 * @property string|null $event_description
 * @property string|null $event_location
 * @property string|null $type
 * @property string|null $event_startdate
 * @property string|null $event_enddate
 * @property int|null $event_target
 * @property string|null $event_banner
 * @property int $status
 * @property string|null $category refer to program ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\UserClient> $client
 * @property-read int|null $client_count
 * @property-read Collection<int, \App\Models\ClientEvent> $clientEvents
 * @property-read int|null $client_events_count
 * @property-read Collection<int, \App\Models\User> $eventPics
 * @property-read int|null $event_pics_count
 * @property-read AgendaSpeaker|null $pivot
 * @property-read Collection<int, \App\Models\User> $internal_speaker
 * @property-read int|null $internal_speaker_count
 * @property-read Collection<int, \App\Models\Corporate> $partner
 * @property-read int|null $partner_count
 * @property-read Collection<int, \App\Models\CorporatePic> $partner_speaker
 * @property-read int|null $partner_speaker_count
 * @property-read Collection<int, \App\Models\School> $school
 * @property-read int|null $school_count
 * @property-read Collection<int, \App\Models\SchoolDetail> $school_speaker
 * @property-read int|null $school_speaker_count
 * @property-read Collection<int, \App\Models\University> $university
 * @property-read int|null $university_count
 * @property-read Collection<int, \App\Models\UniversityPic> $university_speaker
 * @property-read int|null $university_speaker_count
 *
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event search($search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventEnddate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventStartdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Event extends Model
{
    use HasFactory;

    protected $table = 'tbl_events';

    protected $primaryKey = 'event_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'event_id',
        'event_title',
        'event_description',
        'event_location',
        'event_startdate',
        'event_enddate',
        'status',
        'event_target',
        'event_banner',
        'category',
        'type',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_event', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_event', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_event', 'channel_datatable'));

        return $model;
    }

    /**
     * Scopes
     */
    public function scopeSearch($query, $search)
    {
        $terms = $search['terms'] ?? null;
        $query->when($terms, function ($query) use ($terms) {
            $query->where('event_title', 'like', '%'.$terms.'%')
                ->orWhere('event_description', 'like', '%'.$terms.'%');
        });
    }

    protected function eventTarget(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value == null ? 0 : $value,
        );
    }

    public static function whereEventId($id)
    {
        if (is_array($id) && empty($id)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->where('event_id', $id)->first();
    }

    // relation
    public function eventPics()
    {
        return $this->belongsToMany(User::class, 'tbl_event_pic', 'event_id', 'empl_id')->withTimestamps();
    }

    public function university()
    {
        return $this->belongsToMany(University::class, 'tbl_univ_event', 'event_id', 'univ_id');
    }

    public function school()
    {
        return $this->belongsToMany(School::class, 'tbl_sch_event', 'event_id', 'sch_id');
    }

    public function partner()
    {
        return $this->belongsToMany(Corporate::class, 'tbl_corp_partner_event', 'event_id', 'corp_id');
    }

    public function client()
    {
        return $this->hasMany(UserClient::class, 'event_id', 'event_id');
    }

    public function school_speaker()
    {
        return $this->belongsToMany(SchoolDetail::class, 'tbl_agenda_speaker', 'event_id', 'sch_pic_id')->using(AgendaSpeaker::class);
    }

    public function university_speaker()
    {
        return $this->belongsToMany(UniversityPic::class, 'tbl_agenda_speaker', 'event_id', 'univ_pic_id')->using(AgendaSpeaker::class);
    }

    public function partner_speaker()
    {
        return $this->belongsToMany(CorporatePic::class, 'tbl_agenda_speaker', 'event_id', 'partner_pic_id')->using(AgendaSpeaker::class);
    }

    public function internal_speaker()
    {
        return $this->belongsToMany(User::class, 'tbl_agenda_speaker', 'event_id', 'empl_id')->using(AgendaSpeaker::class);
    }

    public function clientEvents()
    {
        return $this->hasMany(ClientEvent::class, 'event_id', 'event_id');
    }
}
