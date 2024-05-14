<?php

namespace App\Models;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'tbl_events';
    protected $primaryKey = 'event_id';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'event_title',
        'event_description',
        'event_location',
        'event_startdate',
        'event_enddate',
        'status',
        'event_target',
        'event_banner',
        'category',
        'type'
    ];

    protected function eventTarget(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value == null ? 0 : $value,
        );
    }

    public static function whereEventId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;
        
        $instance = new static;

        return $instance->newQuery()->where('event_id', $id)->first();
    }

    # relation
    public function eventPic()
    {
        return $this->belongsToMany(User::class, 'tbl_event_pic', 'event_id', 'empl_id')->withTimestamps();
    }

    public function university()
    {
        return $this->belongsToMany(University::class, 'tbl_univ_event', 'event_id', 'univ_id');
    }

    public function school()
    {
        return $this->belongsToMany(School::class, 'tbl_sch_event', 'event_id', 'sch_id');
    }

    public function partner()
    {
        return $this->belongsToMany(Corporate::class, 'tbl_corp_partner_event', 'event_id', 'corp_id');
    }

    public function client()
    {
        return $this->hasMany(UserClient::class, 'event_id', 'event_id');
    }

    public function school_speaker()
    {
        return $this->belongsToMany(SchoolDetail::class, 'tbl_agenda_speaker', 'event_id', 'sch_pic_id')->using(AgendaSpeaker::class);
    }

    public function university_speaker()
    {
        return $this->belongsToMany(UniversityPic::class, 'tbl_agenda_speaker', 'event_id', 'univ_pic_id')->using(AgendaSpeaker::class);
    }

    public function partner_speaker()
    {
        return $this->belongsToMany(CorporatePic::class, 'tbl_agenda_speaker', 'event_id', 'partner_pic_id')->using(AgendaSpeaker::class);
    }

    public function internal_speaker()
    {
        return $this->belongsToMany(User::class, 'tbl_agenda_speaker', 'event_id', 'empl_id')->using(AgendaSpeaker::class);
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'event_id', 'event_id');
    }
}
