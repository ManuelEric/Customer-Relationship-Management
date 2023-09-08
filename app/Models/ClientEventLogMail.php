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
        'clientevent_id',
        'sent_status',
        'category'
    ];

    public function clientEvent()
    {
        return $this->belongsTo(ClientEvent::class, 'clientevent_id', 'clientevent_id');
    }
}
