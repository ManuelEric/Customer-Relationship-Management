<?php

namespace App\Models;

use App\Models\pivot\SchoolCollaboratorFromSchoolProgram;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SchoolProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_sch_prog';

    // public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        // 'id',
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
        'pending_date',
        'accepted_date',
        'cancel_date',
        'reason_id',
        'refund_date',
        'refund_notes',
        'denied_date',
        'empl_id',
    ];

    // public static function whereSchoolProgramId($id)
    // {
    //     if (is_array($id) && empty($id)) return new Collection;

    //     $instance = new static;

    //     return $instance->newQuery()->where('id', $id)->first();
    // }

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'empl_id', 'id');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class, 'reason_id', 'reason_id');
    }

    public function invoiceB2b()
    {
        return $this->belongsTo(Invb2b::class, 'id', 'schprog_id');
    }

    public function school_speaker()
    {
        return $this->belongsToMany(SchoolDetail::class, 'tbl_agenda_speaker', 'sch_prog_id', 'sch_pic_id')->using(AgendaSpeaker::class);
    }

    public function partner_speaker()
    {
        return $this->belongsToMany(CorporatePic::class, 'tbl_agenda_speaker', 'sch_prog_id', 'partner_pic_id')->using(AgendaSpeaker::class);
    }

    public function internal_speaker()
    {
        return $this->belongsToMany(User::class, 'tbl_agenda_speaker', 'sch_prog_id', 'empl_id')->using(AgendaSpeaker::class);
    }

    public function firstDiscuss(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y', strtotime($value)),
        );
    }
    // protected function programName(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => $this->program->prog_program . ' - ' . $this->program->prog_main,
    //     );
    // }

    public function univCollaborators()
    {
        return $this->belongsToMany(University::class, 'tbl_sch_prog_univ', 'schprog_id', 'univ_id')->withTimestamps();
    }

    public function partnerCollaborators()
    {
        return $this->belongsToMany(Corporate::class, 'tbl_sch_prog_partner', 'schprog_id', 'corp_id')->withTimestamps();
    }

    public function schoolCollaborators()
    {
        return $this->belongsToMany(School::class, 'tbl_sch_prog_school', 'schprog_id', 'sch_id')->withTimestamps();
    }

}
