<?php

namespace App\Models;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProg extends Model
{
    use HasFactory;

    protected $table = 'tbl_partner_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'corp_id',
        'prog_id',
        'type',
        'first_discuss',
        'notes',
        'status',
        'participants',
        'start_date',
        'end_date',
        'is_corporate_scheme',
        'reason_id',
        'total_fee',
        'success_date',
        'denied_date',
        'empl_id',
    ];

    public function school_speaker()
    {
        return $this->belongsToMany(SchoolDetail::class, 'tbl_agenda_speaker', 'partner_prog_id', 'sch_pic_id')->using(AgendaSpeaker::class);
    }

    public function partner_speaker()
    {
        return $this->belongsToMany(CorporatePic::class, 'tbl_agenda_speaker', 'partner_prog_id', 'partner_pic_id')->using(AgendaSpeaker::class);
    }

    public function internal_speaker()
    {
        return $this->belongsToMany(User::class, 'tbl_agenda_speaker', 'partner_prog_id', 'empl_id')->using(AgendaSpeaker::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'empl_id', 'id');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class, 'reason_id', 'reason_id');
    }

}
