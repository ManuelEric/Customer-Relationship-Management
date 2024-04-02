<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_prog';
    protected $primaryKey = 'clientprog_id';
    protected $appends = ['referral_name'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        // 'client_uuid',
        'prog_id',
        'lead_id',
        'eduf_lead_id',
        'partner_id',
        'clientevent_id',
        'first_discuss_date',
        'last_discuss_date',
        'followup_date',
        'meeting_date',
        'meeting_notes',
        'status',
        'statusprog_date',
        'initconsult_date',
        'assessmentsent_date',
        'negotiation_date',
        'reason_id',
        'test_date',
        'first_class',
        'last_class',
        'diag_score',
        'test_score',
        'price_from_tutor',
        'our_price_tutor',
        'total_price_tutor',
        'duration_notes',
        'total_uni',
        'total_foreign_currency',
        'foreign_currency_exchange',
        'foreign_currency',
        'total_idr',
        'installment_notes',
        'prog_running_status',
        'prog_start_date',
        'prog_end_date',
        'empl_id',
        'success_date',
        'failed_date',
        'refund_date',
        'refund_notes',
        'timesheet_link',
        'trial_date',
        'session_tutor',
        'registration_type',
        'referral_code',
        'agreement',
        'created_at',
        'updated_at'
    ];

    protected function referralName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->referral_code != NULL ? $this->getReferralNameFromRefCodeView($this->referral_code) : NULL
        );
    }

    public static function whereClientProgramId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->where('clientprog_id', $id)->first();
    }


    /**
     * Scope a query to only include popular users.
     */
    

    # attributes
    protected function programName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->program->prog_program . ' - ' . $this->program->main_prog->main_prog_name,
        );
    }

    protected function invoiceProgramName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->program->prog_main . ': ' . $this->program->prog_program,
        );
    }

    public function getReferralNameFromRefCodeView($refCode)
    {
        // return ViewClientRefCode::whereRaw('ref_code COLLATE utf8mb4_unicode_ci = (?)', $refCode)->first()->full_name;
        return ViewClientRefCode::whereRaw('ref_code = (?)', $refCode)->first()->full_name;
    }

    public function client()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id')->withTrashed();
    }

    public function cleanClient()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }

    public function viewClient()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function external_edufair()
    {
        return $this->belongsTo(EdufLead::class, 'eduf_lead_id', 'id');
    }

    public function partner()
    {
        return $this->belongsTo(Corporate::class, 'partner_id', 'corp_id');
    }

    public function clientEvent()
    {
        return $this->belongsTo(ClientEvent::class, 'clientevent_id', 'clientevent_id');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class, 'reason_id', 'reason_id');
    }

    public function internalPic()
    {
        return $this->belongsTo(User::class, 'empl_id', 'id');
    }

    public function clientMentor()
    {
        return $this->belongsToMany(User::class, 'tbl_client_mentor', 'clientprog_id', 'user_id')->withPivot('type', 'timesheet_link')->withTimestamps();
    }

    public function mentorIC()
    {
        return $this->belongsToMany(User::class, 'tbl_mentor_ic', 'clientprog_id', 'user_id')->withPivot('note')->withTimestamps();
    }

    public function followUp()
    {
        return $this->hasMany(FollowUp::class, 'clientprog_id', 'clientprog_id');
    }

    public function invoice()
    {
        return $this->hasOne(InvoiceProgram::class, 'clientprog_id', 'clientprog_id');
    }

    public function acadTutorDetail()
    {
        return $this->hasMany(AcadTutorDetail::class, 'clientprog_id', 'clientprog_id');
    }

    public function logMail()
    {
        return $this->hasMany(ClientProgramLogMail::class, 'clientprog_id', 'clientprog_id');
    }

    public function viewClientRefCode()
    {
        return $this->belongsTo(ViewClientRefCode::class, 'referral_code', 'ref_code');
    }

    # PIC from sales team
    public function handledBy()
    {
        return $this->belongsToMany(User::class, 'tbl_pic_client', 'client_id', 'user_id');
    }
}
