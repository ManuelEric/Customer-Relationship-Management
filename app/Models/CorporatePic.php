<?php

namespace App\Models;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporatePic extends Model
{
    use HasFactory;

    protected $table = 'tbl_corp_pic';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'corp_id',
        'pic_name',
        'pic_mail',
        'pic_linkedin',
        'pic_phone',
        'is_pic',
    ];

    public function corporate()
    {
        return $this->belongsTo(Corporate::class, 'corp_id', 'corp_id');
    }

    public function as_event_speaker()
    {
        return $this->belongsToMany(Event::class, 'tbl_agenda_speaker', 'partner_pic_id', 'event_id')->using(AgendaSpeaker::class);
    }

    public function as_schoolprog_speaker()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_agenda_speaker', 'partner_pic_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }
}
