<?php

namespace App\Models;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversityPic extends Model
{
    use HasFactory;

    protected $table = 'tbl_univ_pic';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'univ_id',
        'name', 
        'title', 
        'phone',
        'email',
    ];

    public function university()
    {
        return $this->belongsTo(University::class, 'univ_id', 'univ_id');
    }

    public function asSpeaker()
    {
        return $this->belongsToMany(Event::class, 'tbl_agenda_speaker', 'univ_pic_id', 'event_id')->using(AgendaSpeaker::class);
    }
}
