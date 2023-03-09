<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EdufLead extends Model
{
    use HasFactory;

    protected $table = 'tbl_eduf_lead';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'sch_id',
        'corp_id',
        'title',
        'location',
        'intr_pic',
        'ext_pic_name',
        'ext_pic_mail',
        'ext_pic_phone',
        'first_discussion_date',
        'last_discussion_date',
        'event_start',
        'event_end',
        'status',
        'notes'
    ];

    public function client()
    {
        return $this->hasMany(UserClient::class, 'eduf_id', 'id');
    }

    public function schools()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function corps()
    {
        return $this->belongsTo(Corporate::class, 'corp_id', 'corp_id');
    }

    public function review()
    {
        return $this->hasMany(EdufReview::class, 'eduf_id', 'id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'eduf_lead_id', 'id');
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'eduf_id', 'id');
    }
}
