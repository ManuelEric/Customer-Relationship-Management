<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AgendaSpeaker extends Pivot
{
    protected $table = 'tbl_agenda_speaker';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'sch_prog_id', 
        'partner_prog_id', 
        'sch_pic_id', 
        'univ_pic_id',
        'partner_pic_id',
        'empl_id',
        'start_time',
        'end_time',
        'priority',
        'status',
        'speaker_type',
        'notes'
    ];
}
