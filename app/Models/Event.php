<?php

namespace App\Models;

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
        'status'
    ];

    public static function whereEventId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;
        
        $instance = new static;

        return $instance->newQuery()->where('event_id', $id)->first();
    }

    # relation
    public function eventPic()
    {
        return $this->belongsToMany(User::class, 'tbl_event_pic', 'event_id', 'empl_id');
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
}
