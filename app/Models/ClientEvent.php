<?php

namespace App\Models;

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
        'event_id', 
        'lead_id', 
        'joined_date', 
        'status',
    ];

    public function clientProgram()
    {
        return $this->hasOne(ClientProgram::class, 'clientevent_id', 'clientevent_id');
    }

    public function edufLead()
    {
        return $this->belongsTo(EdufLead::class, 'eduf_id', 'id');
    }
}
