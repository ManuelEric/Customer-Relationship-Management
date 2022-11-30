<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch_prog';
    protected $primaryKey = 'id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'sch_id',
        'prog_id',
        'first_discuss',
        'planned_followup',
        'status',
        'notes',
        'notes_detail',
        'running_status',
        'total_hours',
        'total_fee',
        'participants',
        'place',
        'end_program_date',
        'start_program_date',
        'success_date',
        'reason',
        'denied_date',
        'empl_id',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }
}
