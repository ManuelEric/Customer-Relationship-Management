<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SchoolCollaboratorFromSchoolProgram extends Pivot
{
    protected $table = 'tbl_sch_prog_school';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'schprog_id',
        'sch_id',
    ];
}
