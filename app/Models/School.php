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
        'sch_level',
        'sch_curriculum',
        'sch_mail',
        'sch_phone',
        'sch_insta',
        'sch_city',
        'sch_location',
    ];

    public static function whereSchoolId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;
        
        $instance = new static;

        return $instance->newQuery()->find($id, 'sch_id');
    }

    # relation
    public function detail()
    {
        $this->hasMany(SchoolDetail::class, 'sch_id', 'sch_id');
    }
}
