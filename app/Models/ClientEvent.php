<?php

namespace App\Models;

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
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function clientMaster()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }

    public function children()
    {
        return $this->belongsTo(Client::class, 'child_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Client::class, 'parent_id', 'id');
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
