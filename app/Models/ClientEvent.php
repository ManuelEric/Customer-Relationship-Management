<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Observers\ClientEventObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(ClientEventObserver::class)]
/**
 * @property int $clientevent_id
 * @property string|null $ticket_id can be used as identifier
 * @property string $client_id
 * @property string|null $child_id is used when client_id is a parent / they registered as a parent
 * @property string|null $parent_id is used when client_id is a student / they registered as a student
 * @property string|null $event_id
 * @property string $lead_id
 * @property int|null $eduf_id
 * @property string|null $partner_id
 * @property string $registration_type PR : Pra Registration, OTS : On The Spot
 * @property int $number_of_attend How many people are joined the event
 * @property string|null $notes
 * @property string|null $referral_code Referral code is a unique code from client data
 * @property int $status
 * @property string|null $joined_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $children
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\UserClient $clientMaster
 * @property-read \App\Models\ClientProgram|null $clientProgram
 * @property-read \App\Models\EdufLead|null $edufLead
 * @property-read \App\Models\Event|null $event
 * @property-read \App\Models\Lead $lead
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientEventLogMail> $logMail
 * @property-read int|null $log_mail_count
 * @property-read \App\Models\Client|null $parent
 * @property-read \App\Models\Corporate|null $partner
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereChildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereClienteventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereEdufId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereJoinedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereNumberOfAttend($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent wherePartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereRegistrationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEvent whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ClientEvent extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_event';

    protected $primaryKey = 'clientevent_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'ticket_id',
        'child_id',
        'parent_id',
        'event_id',
        'eduf_id',
        'lead_id',
        'partner_id',
        'registration_type',
        'number_of_attend',
        'notes',
        'referral_code',
        'status',
        'joined_date',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_client_event', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        if (isset($attributes['is_many_request']) && $attributes['is_many_request']) {
            unset($attributes['is_many_request']);
        } else {
            // Custom logic after update
            // Send to pusher
            event(new MessageSent('rt_client_event', 'channel_datatable'));
        }

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        if (isset($attributes['is_many_request']) && $attributes['is_many_request']) {
            unset($attributes['is_many_request']);
        } else {
            // Custom logic after create
            // Send to pusher
            event(new MessageSent('rt_client_event', 'channel_datatable'));
        }

        return $model;
    }

    protected function eventId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value)
        );
    }

    public function joinedDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y', strtotime($value)),
        );
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function clientProgram()
    {
        return $this->hasOne(ClientProgram::class, 'clientevent_id', 'clientevent_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id')->withTrashed();
    }

    public function clientMaster()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id')->withTrashed();
    }

    public function children()
    {
        return $this->belongsTo(Client::class, 'child_id', 'id')->withTrashed();
    }

    public function parent()
    {
        return $this->belongsTo(Client::class, 'parent_id', 'id')->withTrashed();
    }

    public function edufLead()
    {
        return $this->belongsTo(EdufLead::class, 'eduf_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function partner()
    {
        return $this->belongsTo(Corporate::class, 'partner_id', 'corp_id');
    }

    public function logMail()
    {
        return $this->hasMany(ClientEventLogMail::class, 'clientevent_id', 'clientevent_id');
    }
}
