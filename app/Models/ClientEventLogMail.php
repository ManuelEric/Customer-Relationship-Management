<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $clientevent_id
 * @property string|null $child_id
 * @property string|null $notes
 * @property string|null $client_id
 * @property string|null $event_id
 * @property int $sent_status
 * @property string|null $category
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @property-read \App\Models\ClientEvent|null $clientEvent
 * @property-read \App\Models\Event|null $event
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereChildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereClienteventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereSentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientEventLogMail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ClientEventLogMail extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_event_log_mail';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'event_id',
        'clientevent_id',
        'index_child',
        'notes',
        'sent_status',
        'category',
        'child_id',
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
