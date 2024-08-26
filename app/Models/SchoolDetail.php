<?php

namespace App\Models;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_schdetail';
    protected $primaryKey = 'schdetail_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'schdetail_fullname',
        'schdetail_email',
        'schdetail_grade',
        'schdetail_position',
        'schdetail_phone',
        'is_pic',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function as_event_speaker()
    {
        return $this->belongsToMany(Event::class, 'tbl_agenda_speaker', 'sch_pic_id', 'event_id')->using(AgendaSpeaker::class);
    }

    public function as_schoolprog_speaker()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_agenda_speaker', 'sch_pic_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }

    public function pic_school_visit()
    {
        return $this->belongsTo(SchoolDetail::class, 'school_pic', 'schdetail_id');
    }
}
