<?php

namespace App\Models;

use App\Models\pivot\ClientAcceptance;
use App\Models\pivot\ClientLeadTracking;
use App\Observers\ClientObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\HasApiTokens;
use Mostafaznv\LaraCache\CacheEntity;
use Mostafaznv\LaraCache\Traits\LaraCache;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([ClientObserver::class])]
class UserClient extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'tbl_client';
    protected $appends = ['lead_source', 'graduation_year_real', 'referral_name'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'st_id',
        'uuid',
        'first_name',
        'last_name',
        'mail',
        'phone',
        'phone_desc',
        'dob',
        'insta',
        'state',
        'city',
        'postal_code',
        'address',
        'sch_id',
        // 'sch_uuid',
        'st_grade',
        'lead_id',
        'eduf_id',
        'partner_id',
        'event_id',
        'st_levelinterest',
        'graduation_year',
        'gap_year',
        'st_abryear',
        // 'st_abrcountry',
        'st_statusact',
        'st_note',
        'st_statuscli',
        // 'st_prospect_status',
        'st_password',
        'preferred_program',
        'is_funding',
        'scholarship',
        'is_verified',
        'register_as',
        'referral_code',
        'category',
        'took_ia',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    # attributes
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($this->last_name) ? $this->first_name . ' ' . $this->last_name : $this->first_name,
        );
    }

    protected function leadSource(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->lead != NULL ? $this->getLeadSource($this->lead->main_lead) : NULL
        );
    }

    protected function clientProgs(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->clientProgram != NULL ? $this->clientProgram : NULL
        );
    }

    protected function gradeNow(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getGradeNowFromView($this->id)
        );
    }

    protected function graduationYearReal(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getGraduationYearFromView($this->id)
        );
    }

    protected function participated(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getParticipatedFromView($this->id)
        );
    }

    protected function referralName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->referral_code != NULL ? $this->getReferralNameFromRefCodeView($this->referral_code) : NULL
        );
    }

    # Scopes
    public function scopeIsVerified($query)
    {
        return $query->where('tbl_client.is_verified', 'Y');
    }

    public function scopeIsNotVerified($query)
    {
        return $query->where('tbl_client.is_verified', 'N');
    }

    public function scopeIsActive($query)
    {
        return $query->where('tbl_client.st_statusact', 1);
    }

    public function scopeIsNotActive($query)
    {
        return $query->where('tbl_client.st_statusact', 0);
    }

    public function scopeIsNotSalesAdmin($query)
    {
        return $query->when(Session::get('user_role') == 'Employee', function ($subQuery) {
            $subQuery->whereHas('handledBy', function ($subQuery_2) {
                $subQuery_2->where('users.id', auth()->user()->id);
            });
            // $subQuery->where('tbl_client.pic_id', auth()->user()->id);
        });
    }

    public function scopeIsUsingAPI($query)
    {
        return $query->when(auth()->guard('api')->user(), function ($subQuery) {
            $subQuery->whereHas('handledBy', function ($subQuery_2) {
                $subQuery_2->where('users.id', auth()->guard('api')->user()->id);
            });
        });
    }

    public function scopeIsStudent($query)
    {
        return $query->whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Student');
        });
    }

    public function scopeHasNoPIC($query)
    {
        return $query->whereNull('pic');
    }

    public function scopeIsParent($query)
    {
        return $query->whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Parent');
        });
    }

    public function scopeIsTeacher($query)
    {
        return $query->whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Teacher/Counselor');
        });
    }

    public function scopeWhereRoleName(Builder $query, $role)
    {
        $query->whereHas('roles', function ($q) use ($role) {
            $q->when(gettype($role) == 'integer', function ($q2) use ($role) {
                $q2->where('id', $role);
            }, function ($q2) use ($role) {
                $q2->where('role_name', $role);
            });
        });
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    public function scopeFilterBasedOnPIC($query)
    {
        return $query->when(Session::get('user_role') == 'Employee', function ($subQuery) {
            $subQuery->whereHas('handledBy', function ($subQuery_2) {
                $subQuery_2->where('users.id', auth()->user()->id);
            });
        });
    }

    public function getLeadSource($parameter)
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

    public function getGraduationYearFromView($id)
    {
        return DB::table('client')->find($id)->graduation_year_real ?? null;
    }

    public function getGradeNowFromView($id)
    {
        return DB::table('client')->find($id)->grade_now ?? null;
    }

    public function getParticipatedFromView($id)
    {
        return DB::table('client')->find($id)->participated;
    }

    public function getReferralNameFromRefCodeView($refCode)
    {
        // return ViewClientRefCode::whereRaw('ref_code COLLATE utf8mb4_unicode_ci = (?)', $refCode)->first()->full_name;
        return ViewClientRefCode::whereRaw('ref_code = (?)', $refCode)->first()->full_name;
    }


    # relation
    public function additionalInfo()
    {
        return $this->hasMany(UserClientAdditionalInfo::class, 'client_id', 'id');
    }

    public function parents()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_relation', 'child_id', 'parent_id')->withTimestamps();
    }

    public function childrens()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_relation', 'parent_id', 'child_id')->withTimestamps()->withTrashed();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_client_roles', 'client_id', 'role_id')->withTimestamps();
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function external_edufair()
    {
        return $this->belongsTo(EdufLead::class, 'eduf_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function destinationCountries()
    {
        return $this->belongsToMany(Tag::class, 'tbl_client_abrcountry', 'client_id', 'tag_id')->withTimestamps();
    }

    public function interestUniversities()
    {
        return $this->belongsToMany(University::class, 'tbl_dreams_uni', 'client_id', 'univ_id')->withTimestamps();
    }

    public function interestPrograms()
    {
        return $this->belongsToMany(Program::class, 'tbl_interest_prog', 'client_id', 'prog_id')->withPivot('id')->withTimestamps();
    }

    public function interestMajor()
    {
        return $this->belongsToMany(Major::class, 'tbl_dreams_major', 'client_id', 'major_id')->withTimestamps();
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'client_id', 'id');
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'client_id', 'id');
    }

    public function viewClientProgram()
    {
        return $this->hasMany(ViewClientProgram::class, 'client_id', 'id');
    }

    public function clientMentor()
    {
        return $this->hasManyThrough(User::class, ClientProgram::class, 'client_id', 'users.id', 'id', 'clientprog_id');
    }

    public function leadStatus()
    {
        return $this->belongsToMany(InitialProgram::class, 'tbl_client_lead_tracking', 'client_id', 'initialprogram_id')->using(ClientLeadTracking::class)->withPivot('type', 'total_result', 'status')->withTimestamps();
    }

    public function universityAcceptance()
    {
        return $this->belongsToMany(University::class, 'tbl_client_acceptance', 'client_id', 'univ_id')->using(ClientAcceptance::class)->withPivot('id', 'status', 'major_id')->withTimestamps();
    }

    public function picClient()
    {
        return $this->hasMany(PicClient::class, 'client_id', 'id');
    }

    public function viewClientRefCode()
    {
        return $this->belongsTo(ViewClientRefCode::class, 'id', 'id');
    }
  
    # PIC from sales team
    public function handledBy()
    {
        return $this->belongsToMany(User::class, 'tbl_pic_client', 'client_id', 'user_id')->withPivot('id', 'status');
    }

    public function followupSchedule()
    {
        return $this->hasMany(FollowupClient::class, 'client_id', 'id');
    }
}
