<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEvent extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_event';
    protected $primaryKey = 'clientevent_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
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

    # Modify methods Model
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

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_client_event', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_client_event', 'channel_datatable'));

        return $model;
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
