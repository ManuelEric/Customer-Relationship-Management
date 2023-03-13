<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


class SchoolProgram extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_schprog';
    protected $primaryKey = 'schprog_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'schprog_id',
        'sch_id',
        'prog_id',
        'schprog_datefirstdis',
        'schprog_datelatedis',
        'schprog_status',
        'schprog_notes',
        'empl_id',
    ];

    public function schoolProgFix()
    {
        return $this->belongsTo(SchoolProgramFix::class, 'schprog_id', 'schprog_id');
    }
}
