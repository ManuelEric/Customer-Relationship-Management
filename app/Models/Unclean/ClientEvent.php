<?php

namespace App\Models\Unclean;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEvent extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

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

   
}
