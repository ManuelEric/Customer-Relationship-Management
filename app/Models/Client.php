<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string|null $id
 * @property int|null $secondary_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $mail
 * @property string|null $phone
 * @property string|null $phone_desc
 * @property string|null $dob
 * @property string|null $insta
 * @property string|null $state
 * @property string|null $city
 * @property int|null $postal_code
 * @property string|null $address
 * @property string|null $sch_id
 * @property int|null $st_grade
 * @property string|null $lead_id
 * @property int|null $eduf_id
 * @property string|null $event_id
 * @property string|null $st_levelinterest
 * @property string|null $graduation_year
 * @property string|null $gap_year
 * @property string|null $st_abryear
 * @property int|null $st_statusact status aktif client
 * @property string|null $st_note
 * @property int|null $st_statuscli 0: prospective, 1: potential, 2: current, 3: completed
 * @property string|null $st_password
 * @property int|null $is_funding 0: False, 1: True
 * @property string|null $register_by
 * @property string|null $preferred_program
 * @property string|null $scholarship Scholarship Eligibility
 * @property string|null $is_verified
 * @property string|null $referral_code Referral code is a unique code from client data
 * @property string|null $category
 * @property string|null $took_ia_date
 * @property int|null $took_ia
 * @property int|null $graduation_year_now
 * @property int|null $grade_now
 * @property int|null $blacklist 0:No, 1:Yes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $full_name
 * @property string|null $referral_name
 * @property string|null $school_name
 * @property string|null $lead_source
 * @property string|null $total_score
 * @property string|null $dream_uni
 * @property string|null $joined_event
 * @property string|null $interest_prog
 * @property string|null $abr_country
 * @property string|null $dream_major
 * @property string|null $program_suggest
 * @property string|null $status_lead
 * @property float|null $status_lead_score
 * @property string|null $group_id
 * @property string|null $participated
 * @property string|null $pic_id
 * @property string|null $pic_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserClient> $childrens
 * @property-read int|null $childrens_count
 * @property-read \App\Models\ClientEvent|null $clientEvent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $clientMentor
 * @property-read int|null $client_mentor_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientProgram> $clientProgram
 * @property-read int|null $client_program_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $destinationCountries
 * @property-read int|null $destination_countries_count
 * @property-read \App\Models\Event|null $event
 * @property-read \App\Models\EdufLead|null $external_edufair
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FollowupClient> $followupSchedule
 * @property-read int|null $followup_schedule_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $handledBy
 * @property-read int|null $handled_by_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Major> $interestMajor
 * @property-read int|null $interest_major_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Program> $interestPrograms
 * @property-read int|null $interest_programs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\University> $interestUniversities
 * @property-read int|null $interest_universities_count
 * @property-read \App\Models\Lead|null $lead
 * @property-read \App\Models\ClientLeadTracking|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InitialProgram> $leadStatus
 * @property-read int|null $lead_status_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientLeadTracking> $leadTracking
 * @property-read int|null $lead_tracking_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserClient> $parents
 * @property-read int|null $parents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\School|null $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\University> $universityAcceptance
 * @property-read int|null $university_acceptance_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client doesntHavePIC()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isActive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isNotActive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isNotBlacklist()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isNotSalesAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isNotVerified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isParent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isStudent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isUsingAPI()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client isVerified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereAbrCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereBlacklist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDreamMajor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDreamUni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereEdufId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereGapYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereGradeNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereGraduationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereGraduationYearNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereInsta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereInterestProg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereIsFunding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereJoinedEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereLeadSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereParticipated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePhoneDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePreferredProgram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereProgramSuggest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereReferralName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereRegisterBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereScholarship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSchoolName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSecondaryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStAbryear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStLevelinterest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStStatusact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStStatuscli($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStatusLead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStatusLeadScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereTookIa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereTookIaDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereTotalScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client withAndWhereHas($relation, $constraint)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $table = 'client';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'secondary_id',
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
        'st_grade',
        'lead_id',
        'eduf_id',
        'partner_id',
        'event_id',
        'st_levelinterest',
        'graduation_year',
        'graduation_year_now',
        'grade_now',
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
        'register_by',
        'is_verified',
        'created_at',
        'updated_at',
        'deleted_at',
        'pic_id',
        'status_lead',
        'status_lead_score',
        'blacklist',
    ];

    // attributes
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($this->last_name) ? $this->first_name.' '.$this->last_name : $this->first_name,
        );
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    // Scopes
    public function scopeIsNotBlacklist($query)
    {
        return $query->where('client.blacklist', 0);
    }

    public function scopeIsVerified($query)
    {
        return $query->where('client.is_verified', 'Y');
    }

    public function scopeIsNotVerified($query)
    {
        return $query->where('client.is_verified', 'N');
    }

    public function scopeIsActive($query)
    {
        return $query->where('client.st_statusact', 1);
    }

    public function scopeIsNotActive($query)
    {
        return $query->where('client.st_statusact', 0);
    }

    public function scopeIsStudent($query)
    {
        return $query->whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Student');
        });
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

    public function scopeIsNotSalesAdmin($query)
    {
        return $query->when(Session::get('user_role') == 'Employee', function ($subQuery) {
            $subQuery->where('client.pic_id', auth()->user()->id);
        });
    }

    public function scopeDoesntHavePIC($query)
    {
        return $query->when(Session::get('user_role') == 'Employee', function ($subQuery) {
            $subQuery->where('client.pic_id', auth()->user()->id)->orWhereNull('client.pic_id');
        });
    }

    public function scopeIsUsingAPI($query)
    {
        return $query->when(auth()->guard('api')->user() && Session::get('user_role') == 'Employee', function ($subQuery) {
            $subQuery->whereHas('handledBy', function ($subQuery_2) {
                $subQuery_2->where('users.id', auth()->guard('api')->user()->id);
            });
        });
    }

    // attributes
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('d M Y H:i:s', strtotime($value)),
        );
    }

    // protected function showGrade(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => $this->grade_now > 12 ? "Not high school" : $this->grade_now
    //     );
    // }

    public function parents()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_relation', 'child_id', 'parent_id');
    }

    public function childrens()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_relation', 'parent_id', 'child_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_client_roles', 'client_id', 'role_id');
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
        return $this->belongsToMany(Tag::class, 'tbl_client_abrcountry', 'client_id', 'country_id');
    }

    public function interestUniversities()
    {
        return $this->belongsToMany(University::class, 'tbl_dreams_uni', 'client_id', 'univ_id');
    }

    public function interestPrograms()
    {
        return $this->belongsToMany(Program::class, 'tbl_interest_prog', 'client_id', 'prog_id')->withPivot('id')->withTimestamps();
    }

    public function interestMajor()
    {
        return $this->belongsToMany(Major::class, 'tbl_dreams_major', 'client_id', 'major_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'client_id', 'id');
    }

    public function clientEvent()
    {
        return $this->hasOne(ClientEvent::class, 'event_id', 'id');
    }

    public function clientMentor()
    {
        return $this->hasManyThrough(User::class, ClientProgram::class, 'client_id', 'users.id', 'id', 'clientprog_id');
    }

    public function leadTracking()
    {
        return $this->hasMany(ClientLeadTracking::class, 'client_id', 'id');
    }

    public function universityAcceptance()
    {
        return $this->belongsToMany(University::class, 'tbl_client_acceptance', 'client_id', 'univ_id')->withPivot('tbl_client_acceptance.status')->withTimestamps();
    }

    public function leadStatus()
    {
        return $this->belongsToMany(InitialProgram::class, 'tbl_client_lead_tracking', 'client_id', 'initialprogram_id')->using(ClientLeadTracking::class)->withPivot('type', 'total_result', 'status')->withTimestamps();
    }

    // PIC from sales team
    public function handledBy()
    {
        return $this->belongsToMany(User::class, 'tbl_pic_client', 'client_id', 'user_id');
    }

    public function followupSchedule()
    {
        return $this->hasMany(FollowupClient::class, 'client_id', 'id');
    }
}
