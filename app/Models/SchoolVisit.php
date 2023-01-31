<?php

namespace App\Models;

use App\Models\SchoolDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolVisit extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch_visit';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'sch_id',
        'internal_pic',
        'school_pic',
        'visit_date',
        'notes',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function pic_from_allin()
    {
        return $this->belongsTo(User::class, 'internal_pic', 'id');
    }

    public function pic_from_school()
    {
        return $this->belongsTo(SchoolDetail::class, 'school_pic', 'schdetail_id');
    }
}
