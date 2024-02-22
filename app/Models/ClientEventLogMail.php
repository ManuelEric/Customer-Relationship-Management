<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEventLogMail extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_event_log_mail';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'event_id',
        'clientevent_id',
        'index_child',
        'child_id',
        'notes',
        'sent_status',
        'category'
    ];

    public function clientEvent()
    {
        return $this->belongsTo(ClientEvent::class, 'clientevent_id', 'clientevent_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }
}
