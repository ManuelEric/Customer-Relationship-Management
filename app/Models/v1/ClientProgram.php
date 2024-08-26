<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProgram extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_stprog';
    protected $primaryKey = 'stprog_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'st_num',
        'prog_id',
        'lead_id',
        'eduf_id',
        'infl_id',
        'stprog_firstdisdate',
        'stprog_followupdate',
        'stprog_lastdisdate',
        'stprog_meetingdate',
        'stprog_meetingnote',
        'stprog_status',
        'stprog_statusprogdate',
        'stprog_init_consult',
        'stprog_ass_sent',
        'stprog_nego',
        'reason_id',
        'stprog_test_date',
        'stprog_last_class',
        'stprog_diag_score',
        'stprog_test_score',
        'stprog_price_from_tutor',
        'stprog_our_price_tutor',
        'stprog_total_price_tutor',
        'stprog_duration',
        'stprog_tot_uni',
        'stprog_tot_dollar',
        'stprog_kurs',
        'stprog_tot_idr',
        'stprog_install_plan',
        'stprog_runningstatus',
        'stprog_start_date',
        'stprog_end_date',
        'empl_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'st_num', 'st_num');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class, 'reason_id', 'reason_id');
    }

    public function pic()
    {
        return $this->belongsTo(Employee::class, 'empl_id', 'empl_id');
    }

    public function hasMentors()
    {
        return $this->hasMany(StMentor::class, 'stprog_id', 'stprog_id');
    }

    public function hasMainMentor()
    {
        return $this->belongsToMany(Mentor::class, 'tbl_stmentor', 'stprog_id', 'mt_id1');
    }

    public function hasBackupMentor()
    {
        return $this->belongsToMany(Mentor::class, 'tbl_stmentor', 'stprog_id', 'mt_id2');
    }

    public function followUp()
    {
        return $this->hasMany(FollowUp::class, 'stprog_id', 'stprog_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'stprog_id', 'stprog_id');
    }
}
