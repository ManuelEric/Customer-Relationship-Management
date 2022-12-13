<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch';
    protected $primaryKey = 'sch_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'sch_id',
        'sch_name',
        'sch_type',
        'sch_mail',
        'sch_phone',
        'sch_insta',
        'sch_city',
        'sch_location',
        'sch_score',
    ];

    public static function whereSchoolId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->where('sch_id', $id)->first();
    }

    # relation
    public function detail()
    {
        return $this->hasMany(SchoolDetail::class, 'sch_id', 'sch_id');
    }

    public function edufair()
    {
        return $this->hasMany(EdufLead::class, 'sch_id', 'sch_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'tbl_sch_event', 'sch_id', 'event_id');
    }

    public function client()
    {
        return $this->hasMany(UserClient::class, 'sch_id', 'sch_id');
    }
    
    public function schoolProgram()
    {
        return $this->hasMany(SchoolProgram::class, 'sch_id', 'sch_id');
    }

    public function curriculum()
    {
        return $this->belongsToMany(Curriculum::class, 'tbl_sch_curriculum', 'sch_id', 'curriculum_id');
    }
}
