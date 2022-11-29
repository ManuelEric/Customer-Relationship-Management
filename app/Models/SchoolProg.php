<?php

namespace App\Models;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProg extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'sch_id',
        'prog_id',
        'first_discuss',
        'last_discuss',
        'status',
        'notes',
        'empl_id',
    ];

    public function pic_speaker()
    {
        return $this->belongsToMany(SchoolDetail::class, 'tbl_agenda_speaker', 'sch_pic_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }

    public function partner_speaker()
    {
        return $this->belongsToMany(CorporatePic::class, 'tbl_agenda_speaker', 'partner_pic_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }

    public function internal_speaker()
    {
        return $this->belongsToMany(User::class, 'tbl_agenda_speaker', 'empl_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }
}
