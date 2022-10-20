<?php

namespace App\Models;

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
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }
}
