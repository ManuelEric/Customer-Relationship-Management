<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'tbl_curriculum';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function school()
    {
        return $this->belongsToMany(School::class, 'tbl_sch_curriculum', 'curriculum_id', 'sch_id');
    }
}
