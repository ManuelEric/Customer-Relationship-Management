<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_students';
    protected $primaryKey = 'st_num';
    

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'st_id',
        'pr_id',
        'st_firstname',
        'st_lastname',
        'st_mail',
        'st_phone',
        'st_dob',
        'st_insta',
        'st_state',
        'st_city',
        'st_address',
        'sch_id',
        'st_grade',
        'st_grade_updated',
        'lead_id',
        'eduf_id',
        'infl_id',
        'st_levelinterest',
        'prog_id',
        'st_abryear',
        'st_abrcountry',
        'st_abruniv',
        'st_abrmajor',
        'st_statusact',
        'st_note',
        'st_statuscli',
        'st_password',
        'st_datecreate',
        'st_datelastedit',
    ];

    public function parent()
    {
        return $this->belongsTo(StudentParent::class, 'pr_id', 'pr_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function eduf()
    {
        return $this->belongsTo(Edufair::class, 'eduf_id', 'eduf_id');
    }

    public function interestedProgram()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'st_num', 'st_num');
    }
}
