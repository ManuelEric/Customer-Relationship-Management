<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

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
        'sch_level',
        'sch_curriculum',
        'sch_mail',
        'sch_phone',
        'sch_insta',
        'sch_city',
        'sch_location',
        'sch_lastupdate',
    ];

    public function student()
    {
        return $this->hasMany(Student::class, 'sch_id', 'sch_id');
    }
}
