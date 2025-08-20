<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Models\pivot\ClientProgramDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $clientprog_id
 * @property string $client_id
 * @property string $prog_id
 * @property string $lead_id
 * @property int|null $eduf_lead_id
 * @property string|null $partner_id
 * @property int|null $clientevent_id
 * @property string|null $first_discuss_date
 * @property string|null $meeting_notes
 * @property int $status 0: pending, 1: success, 2: failed, 3: refund, 4: hold, 5: stop
 * @property string|null $initconsult_date
 * @property string|null $assessmentsent_date
 * @property string|null $negotiation_date
 * @property int|null $reason_id
 * @property string|null $reason_notes
 * @property string|null $test_date
 * @property string|null $first_class
 * @property string|null $last_class
 * @property int $diag_score
 * @property int $test_score
 * @property int $price_from_tutor
 * @property int $our_price_tutor
 * @property int $total_price_tutor
 * @property string|null $duration_notes
 * @property int $total_uni
 * @property int $total_foreign_currency
 * @property int $foreign_currency_exchange
 * @property string|null $foreign_currency
 * @property int $total_idr
 * @property string|null $installment_notes
 * @property int $prog_running_status 0: not yet, 1: ongoing, 2: done
 * @property string|null $prog_start_date
 * @property string|null $prog_end_date
 * @property string|null $empl_id
 * @property string|null $package Tutoring package
 * @property string|null $curriculum Tutoring curriculum
 * @property string|null $hold_date a date that created to inform when holding process started
 * @property string|null $success_date
 * @property string|null $failed_date
 * @property string|null $refund_date
 * @property string|null $refund_notes
 * @property string|null $timesheet_link
 * @property string|null $trial_date
 * @property int|null $session_tutor for academic tutor only
 * @property string|null $registration_type FE: Form Embed, I: Import
 * @property string|null $referral_code Referral code is a unique code from client data
 * @property string|null $agreement file path
 * @property string|null $agreement_uploaded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\AcadTutorDetail> $acadTutorDetail
 * @property-read int|null $acad_tutor_detail_count
 * @property-read \App\Models\BundlingDetail|null $bundlingDetail
 * @property-read \App\Models\UserClient $cleanClient
 * @property-read \App\Models\UserClient $client
 * @property-read \App\Models\ClientEvent|null $clientEvent
 * @property-read Collection<int, \App\Models\User> $clientMentor
 * @property-read int|null $client_mentor_count
 * @property-read Collection<int, \App\Models\ClientLog> $client_log
 * @property-read int|null $client_log_count
 * @property-read mixed $conversion_lead
 * @property-read mixed $conversion_time
 * @property-read \App\Models\EdufLead|null $external_edufair
 * @property-read Collection<int, \App\Models\FollowUp> $followUp
 * @property-read int|null $follow_up_count
 * @property-read Collection<int, \App\Models\User> $handledBy
 * @property-read int|null $handled_by_count
 * @property-read \App\Models\User|null $internalPic
 * @property-read \App\Models\InvoiceProgram|null $invoice
 * @property-read mixed $invoice_program_name
 * @property-read \App\Models\Lead $lead
 * @property-read Collection<int, \App\Models\ClientProgramLogMail> $logMail
 * @property-read int|null $log_mail_count
 * @property-read Collection<int, \App\Models\User> $mentorIC
 * @property-read int|null $mentor_i_c_count
 * @property-read \App\Models\Corporate|null $partner
 * @property-read ClientProgramDetail|null $pivot
 * @property-read Collection<int, \App\Models\PhaseDetail> $phase_detail
 * @property-read int|null $phase_detail_count
 * @property-read Collection<int, \App\Models\PhaseLibrary> $phase_library
 * @property-read int|null $phase_library_count
 * @property-read \App\Models\Program $program
 * @property-read \App\Models\Reason|null $reason
 * @property-read mixed $referral_name
 * @property-read mixed $strip_tag_notes
 * @property-read \App\Models\Client $viewClient
 * @property-read \App\Models\ViewClientRefCode|null $viewClientRefCode
 * @property-read \App\Models\ViewProgram $viewProgram
 *
 * @method static Builder<static>|ClientProgram alreadyPaidTheProgram(\Carbon\Carbon $start_date, \Carbon\Carbon $end_date)
 * @method static Builder<static>|ClientProgram dealLeads(\Carbon\Carbon $start_date, \Carbon\Carbon $end_date)
 * @method static Builder<static>|ClientProgram gIP()
 * @method static Builder<static>|ClientProgram getFreeTrial()
 * @method static Builder<static>|ClientProgram hasAgreement(\Carbon\Carbon $start_date, \Carbon\Carbon $end_date)
 * @method static Builder<static>|ClientProgram mentoring()
 * @method static Builder<static>|ClientProgram newModelQuery()
 * @method static Builder<static>|ClientProgram newQuery()
 * @method static Builder<static>|ClientProgram offline()
 * @method static Builder<static>|ClientProgram onlineOrganic()
 * @method static Builder<static>|ClientProgram onlinePaid()
 * @method static Builder<static>|ClientProgram pending()
 * @method static Builder<static>|ClientProgram query()
 * @method static Builder<static>|ClientProgram referral()
 * @method static Builder<static>|ClientProgram success()
 * @method static Builder<static>|ClientProgram successAndPaid()
 * @method static Builder<static>|ClientProgram tutoring()
 * @method static Builder<static>|ClientProgram whereAgreement($value)
 * @method static Builder<static>|ClientProgram whereAgreementUploadedAt($value)
 * @method static Builder<static>|ClientProgram whereAssessmentsentDate($value)
 * @method static Builder<static>|ClientProgram whereClientId($value)
 * @method static Builder<static>|ClientProgram whereClienteventId($value)
 * @method static Builder<static>|ClientProgram whereClientprogId($value)
 * @method static Builder<static>|ClientProgram whereCreatedAt($value)
 * @method static Builder<static>|ClientProgram whereCurriculum($value)
 * @method static Builder<static>|ClientProgram whereDiagScore($value)
 * @method static Builder<static>|ClientProgram whereDurationNotes($value)
 * @method static Builder<static>|ClientProgram whereEdufLeadId($value)
 * @method static Builder<static>|ClientProgram whereEmplId($value)
 * @method static Builder<static>|ClientProgram whereFailedDate($value)
 * @method static Builder<static>|ClientProgram whereFirstClass($value)
 * @method static Builder<static>|ClientProgram whereFirstDiscussDate($value)
 * @method static Builder<static>|ClientProgram whereForeignCurrency($value)
 * @method static Builder<static>|ClientProgram whereForeignCurrencyExchange($value)
 * @method static Builder<static>|ClientProgram whereHoldDate($value)
 * @method static Builder<static>|ClientProgram whereInitconsultDate($value)
 * @method static Builder<static>|ClientProgram whereInstallmentNotes($value)
 * @method static Builder<static>|ClientProgram whereLastClass($value)
 * @method static Builder<static>|ClientProgram whereLeadId($value)
 * @method static Builder<static>|ClientProgram whereMeetingNotes($value)
 * @method static Builder<static>|ClientProgram whereNegotiationDate($value)
 * @method static Builder<static>|ClientProgram whereOurPriceTutor($value)
 * @method static Builder<static>|ClientProgram wherePackage($value)
 * @method static Builder<static>|ClientProgram wherePartnerId($value)
 * @method static Builder<static>|ClientProgram wherePriceFromTutor($value)
 * @method static Builder<static>|ClientProgram whereProgEndDate($value)
 * @method static Builder<static>|ClientProgram whereProgId($value)
 * @method static Builder<static>|ClientProgram whereProgRunningStatus($value)
 * @method static Builder<static>|ClientProgram whereProgStartDate($value)
 * @method static Builder<static>|ClientProgram whereReasonId($value)
 * @method static Builder<static>|ClientProgram whereReasonNotes($value)
 * @method static Builder<static>|ClientProgram whereReferralCode($value)
 * @method static Builder<static>|ClientProgram whereRefundDate($value)
 * @method static Builder<static>|ClientProgram whereRefundNotes($value)
 * @method static Builder<static>|ClientProgram whereRegistrationType($value)
 * @method static Builder<static>|ClientProgram whereSessionTutor($value)
 * @method static Builder<static>|ClientProgram whereStatus($value)
 * @method static Builder<static>|ClientProgram whereSuccessDate($value)
 * @method static Builder<static>|ClientProgram whereTestDate($value)
 * @method static Builder<static>|ClientProgram whereTestScore($value)
 * @method static Builder<static>|ClientProgram whereTimesheetLink($value)
 * @method static Builder<static>|ClientProgram whereTotalForeignCurrency($value)
 * @method static Builder<static>|ClientProgram whereTotalIdr($value)
 * @method static Builder<static>|ClientProgram whereTotalPriceTutor($value)
 * @method static Builder<static>|ClientProgram whereTotalUni($value)
 * @method static Builder<static>|ClientProgram whereTrialDate($value)
 * @method static Builder<static>|ClientProgram whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ClientProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_prog';

    protected $primaryKey = 'clientprog_id';

    protected $appends = ['strip_tag_notes', 'referral_name'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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
        'package',
        'curriculum',
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
        'updated_at',
    ];

    // Modify methods Model
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

        if (isset($attributes['is_many_request']) && $attributes['is_many_request']) {
            unset($attributes['is_many_request']);
        } else {
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

        if (isset($attributes['is_many_request']) && $attributes['is_many_request']) {
            unset($attributes['is_many_request']);
        } else {
            // Custom logic after create
            // Send to pusher
            event(new MessageSent('rt_client_program', 'channel_datatable'));
        }

        return $model;
    }

    protected function conversionLead(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->lead != null ? $this->getConversionLead($this->lead->main_lead) : null
        );
    }

    public function getConversionLead($parameter)
    {
        switch ($parameter) {
            case 'All-In Event':
                if ($this->event != null) {
                    return 'ALL-In Event - '.$this->event->event_title;
                } else {
                    return 'ALL-In Event';
                }
                break;

            case 'External Edufair':
                if ($this->eduf_id == null) {
                    return $this->lead->main_lead;
                }

                if ($this->external_edufair->title != null) {
                    return 'External Edufair - '.$this->external_edufair->title;
                } else {
                    return 'External Edufair - '.$this->external_edufair->organizerName;
                }
                break;

            case 'KOL':
                return 'KOL - '.$this->lead->sub_lead;
                break;

            default:
                return $this->lead->main_lead;
        }
    }

    protected function referralName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->referral_code != null ? $this->getReferralNameFromRefCodeView($this->referral_code) : null
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
        if (is_array($id) && empty($id)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->where('clientprog_id', $id)->first();
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', 0);
    }

    public function scopeGetFreeTrial(Builder $query): void
    {
        $query->whereNotNull('trial_date')->orWhere('package', 'Free Trial');
    }

    public function scopeSuccessAndPaid(Builder $query): void
    {
        $query->
            where('tbl_client_prog.status', 1)->
            whereNot('tbl_client_prog.prog_running_status', 2)->
            // where(function ($query) {
            //     $query->
            //         where('prog_end_date', '>=', Carbon::now())->
            //         orWhere('last_class', '>=', Carbon::now());
            // })->
            where(function ($query2) {
                $query2->has('invoice')->has('invoice.receipt');
            });
    }

    /**
     * Scope a query to only include popular users.
     */

    // attributes
    protected function conversionTime(): Attribute
    {
        $successDate = Carbon::parse($this->success_date);
        $firstDiscussDate = Carbon::parse($this->first_discuss_date);

        return Attribute::make(
            get: fn ($value) => $firstDiscussDate->diffInDays($successDate),
        );
    }

    protected function invoiceProgramName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => DB::select('SELECT StringProgramName(?) AS program_name', [$this->clientprog_id])[0]->program_name
        );
    }

    public function getReferralNameFromRefCodeView($refCode)
    {
        return UserClient::where('secondary_id', $refCode)->first()->full_name ?? null;
        // return ViewClientRefCode::whereRaw('ref_code = (?)', $refCode)->first()->full_name;
    }

    /**
     * Scopes
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
        $lead_of_referral = ['LS005', 'LS058', 'LS060', 'LS061']; // manually select lead from referral
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
        // ! withTrashed() > could be deleted
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
        return $this->belongsToMany(User::class, 'tbl_client_mentor', 'clientprog_id', 'user_id')->withPivot('id', 'type', 'timesheet_link', 'status')->withTimestamps();
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

    // PIC from sales team
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
        return $this->belongsToMany(PhaseLibrary::class, 'client_program_details', 'clientprog_id', 'phase_lib_id')->using(ClientProgramDetail::class)->withPivot('quota', 'use');
    }

    public function phase_detail()
    {
        return $this->belongsToMany(PhaseDetail::class, 'client_program_details', 'clientprog_id', 'phase_detail_id')->using(ClientProgramDetail::class)->withPivot('quota', 'use');
    }
}
