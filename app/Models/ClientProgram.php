<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\pivot\ClientProgramDetail;

class ClientProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_prog';
    protected $primaryKey = 'clientprog_id';
    protected $appends = ['strip_tag_notes', 'referral_name'];

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
        'reason_notes',
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
        'hold_date',
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
        'agreement_uploaded_at',
        'created_at',
        'updated_at'
    ];

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_client_program', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        if(isset($attributes['is_many_request']) && $attributes['is_many_request'])
        {
            unset($attributes['is_many_request']);
        }else{
            // Custom logic after update
            // Send to pusher
            event(new MessageSent('rt_client_program', 'channel_datatable'));
        }

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        if(isset($attributes['is_many_request']) && $attributes['is_many_request'])
        {
            unset($attributes['is_many_request']);
        }else{
            // Custom logic after create
            // Send to pusher
            event(new MessageSent('rt_client_program', 'channel_datatable'));
        }

        return $model;
    }

    protected function conversionLead(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->lead != NULL ? $this->getConversionLead($this->lead->main_lead) : NULL
        );
    }

    public function getConversionLead($parameter)
    {
        switch ($parameter) {
            case "All-In Event":
                if ($this->event != NULL)
                    return "ALL-In Event - " . $this->event->event_title;
                else
                    return "ALL-In Event";
                break;

            case "External Edufair":
                if($this->eduf_id == NULL){
                    return $this->lead->main_lead;
                }

                if ($this->external_edufair->title != NULL)
                    return "External Edufair - " . $this->external_edufair->title;
                else
                    return "External Edufair - " . $this->external_edufair->organizerName;
                break;

            case "KOL":
                return "KOL - " . $this->lead->sub_lead;
                break;

            default:
                return $this->lead->main_lead;
        }
    }

    protected function referralName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->referral_code != NULL ? $this->getReferralNameFromRefCodeView($this->referral_code) : NULL
        );
    }

    protected function stripTagNotes(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => mb_substr(strip_tags($this->meeting_notes), 0, 50)
        );
    }

    public static function whereClientProgramId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->where('clientprog_id', $id)->first();
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', 0);
    }

    public function scopeGetFreeTrial(Builder $query): void
    {
        $query->whereNotNull('trial_date');
    }

    public function scopeSuccessAndPaid(Builder $query): void
    {
        $query->
            where('status', 1)->
            whereNot('prog_running_status', 2)->where('prog_end_date', '>=', Carbon::now())->
            where(function ($query2) {
                $query2->has('invoice')->has('invoice.receipt');
            });
    }


    /**
     * Scope a query to only include popular users.
     */
    

    # attributes
    protected function conversionTime(): Attribute
    {
        $successDate = Carbon::parse($this->success_date);
        $firstDiscussDate = Carbon::parse($this->first_discuss_date);

        return Attribute::make(
            get: fn ($value) => $firstDiscussDate->diffInDays($successDate),
        );
    }

    protected function programName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
        );
    }

    protected function invoiceProgramName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->program->main_prog->prog_name . ': ' . $this->program->prog_program,
        );
    }

    public function getReferralNameFromRefCodeView($refCode)
    {
        return UserClient::where('secondary_id', $refCode)->first()->full_name ?? null;
        // return ViewClientRefCode::whereRaw('ref_code = (?)', $refCode)->first()->full_name;
    }

    /**
     * Scopes
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeOnlinePaid(Builder $query): void
    {
        $query->whereHas('lead', function ($sub) {
            $sub->where('type', 'paid')->where('is_online', true);
        });
    }

    public function scopeOnlineOrganic(Builder $query): void
    {
        $query->whereHas('lead', callback: function ($sub) {
            $sub->where('type', 'organic')->where('is_online', true);
        });
    }

    public function scopeOffline(Builder $query): void
    {
        $lead_of_referral = ['LS005', 'LS058', 'LS060', 'LS061'];
        $query->whereHas('lead', function ($sub) use ($lead_of_referral) {
            $sub->where('is_online', false)->whereNotIn('lead_id', $lead_of_referral);
        });
    }


    public function scopeReferral(Builder $query): void
    {
        $lead_of_referral = ['LS005', 'LS058', 'LS060', 'LS061']; # manually select lead from referral
        $query->whereHas('lead', function ($sub) use ($lead_of_referral) {
            $sub->whereIn('lead_id', $lead_of_referral);
        });
    }

    public function scopeMentoring(Builder $query): void
    {
        $query->whereHas('program.main_prog', function ($sub) {
            $sub->where('prog_name', 'Admissions Mentoring');
        });
    }

    public function scopeTutoring(Builder $query): void
    {
        $query->whereHas('program.main_prog', function ($sub) {
            $sub->where('prog_name', 'Academic & Test Preparation');
        });
    }

    public function scopeGIP(Builder $query): void
    {
        $query->whereHas('program.sub_prog', function ($sub) {
            $sub->where('sub_prog_name', 'Global Immersion Program');
        });
    }

    public function scopeDealLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->whereIn('status', [1, 4])->whereBetween('success_date', [$start_date, $end_date]);
    }

    public function scopeHasAgreement(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->whereNotNull('agreement')->whereBetween('success_date', [$start_date, $end_date]);
    }

    public function scopeAlreadyPaidTheProgram(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->whereHas('invoice.firstReceipt', function ($sub) use ($start_date, $end_date) {
            $sub->whereBetween('receipt_date', [$start_date, $end_date]);
        });
    }

    public function scopeSuccess(Builder $query): void
    {
        $query->whereNotNull('success_date')->whereNull('failed_date')->whereNull('refund_date');
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
        //! withTrashed() > could be deleted
        return $this->belongsTo(Client::class, 'client_id', 'id')->withTrashed();
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

    public function viewProgram()
    {
        return $this->belongsTo(ViewProgram::class, 'prog_id', 'prog_id');
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

    public function bundlingDetail()
    {
        return $this->hasOne(BundlingDetail::class, 'clientprog_id', 'clientprog_id');
    }

    public function client_log()
    {
        return $this->hasMany(ClientLog::class, 'clientprog_id', 'clientprog_id');
    }

    public function phase_library()
    {
        return $this->belongsToMany(PhaseLibrary::class, 'client_program_details', 'clientprog_id', 'phase_lib_id')->using(ClientProgramDetail::class);
    }

}
