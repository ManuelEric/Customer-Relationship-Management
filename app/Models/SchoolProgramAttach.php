<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProgramAttach extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch_prog_attach';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'schprog_id',
        'schprog_file',
        'schprog_attach',
    ];

    public function school_program()
    {
        return $this->belongsTo(SchoolProgram::class, 'schprog_id', 'id');
    }
}
