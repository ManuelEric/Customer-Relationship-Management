<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Http\Traits\CleanStringTrait;
use App\Jobs\Client\ProcessUpdateGradeAndGraduationYearNow;
use App\Models\Mentoring\MentoringLog;
use App\Models\pivot\ClientAcceptance;
use App\Models\pivot\ClientLeadTracking;
use App\Models\pivot\ClientMentor;
use App\Observers\UserClientObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int|null $graduation_year
 * @property int|null $application_year
 * @property int|null $graduation_year_now
 * @property string $id
 * @property int $secondary_id
 * @property string $first_name
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
 * @property string|null $gap_year
 * @property string|null $st_abryear
 * @property int $st_statusact status aktif client
 * @property string|null $st_note
 * @property int $st_statuscli 0: prospective, 1: potential, 2: current, 3: completed
 * @property string|null $st_password
 * @property int $is_funding 0: False, 1: True
 * @property string|null $register_by
 * @property string|null $preferred_program
 * @property string $scholarship Scholarship Eligibility
 * @property string $is_verified
 * @property string|null $referral_code Referral code is a unique code from client data
 * @property string|null $category
 * @property string|null $utm_content
 * @property string|null $took_ia_date
 * @property int $took_ia
 * @property int|null $grade_now
 * @property string|null $graduated_status
 * @property int $blacklist 0:No, 1:Yes
 * @property string|null $mentoring_progress_status on track, slow, behind, halt
 * @property string|null $mentoring_google_drive_link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserClientAdditionalInfo> $additionalInfo
 * @property-read int|null $additional_info_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserClient> $childrens
 * @property-read int|null $childrens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientEvent> $clientEvent
 * @property-read int|null $client_event_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ClientMentor> $clientMentor
 * @property-read int|null $client_mentor_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientProgram> $clientProgram
 * @property-read int|null $client_program_count
 * @property-read mixed $client_progs
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientLog> $client_log
 * @property-read int|null $client_log_count
 * @property-read ClientLeadTracking|ClientAcceptance|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\University> $decidedUniversityAcceptance
 * @property-read int|null $decided_university_acceptance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MasterCountry> $destinationCountries
 * @property-read int|null $destination_countries_count
 * @property-read \App\Models\Event|null $event
 * @property-read \App\Models\EdufLead|null $external_edufair
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FollowupClient> $followupSchedule
 * @property-read int|null $followup_schedule_count
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $handledBy
 * @property-read int|null $handled_by_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Major> $interestMajor
 * @property-read int|null $interest_major_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Program> $interestPrograms
 * @property-read int|null $interest_programs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\University> $interestUniversities
 * @property-read int|null $interest_universities_count
 * @property-read \App\Models\ClientProgram|null $latestAdmissionProgram
 * @property-read \App\Models\ClientProgram|null $latestNonAdmissionProgram
 * @property-read \App\Models\ClientProgram|null $latestOfferedProgram
 * @property-read \App\Models\Lead|null $lead
 * @property-read mixed $lead_source
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InitialProgram> $leadStatus
 * @property-read int|null $lead_status_count
 * @property-read mixed $list_interest_countries
 * @property-read mixed $list_interest_progs
 * @property-read mixed $list_joined_events
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Major> $majorAcceptance
 * @property-read int|null $major_acceptance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MajorGroup> $majorGroupAcceptance
 * @property-read int|null $major_group_acceptance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MentoringLog> $mentoringLogs
 * @property-read int|null $mentoring_logs_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserClient> $parents
 * @property-read int|null $parents_count
 * @property-read mixed $participated
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PicClient> $picClient
 * @property-read int|null $pic_client_count
 * @property-read mixed $pic_id
 * @property-read mixed $pic_name
 * @property-read mixed $referral_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\School|null $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\University> $universityAcceptance
 * @property-read int|null $university_acceptance_count
 * @property-read \App\Models\ViewClientRefCode|null $viewClientRefCode
 *
 * @method static \Database\Factories\UserClientFactory factory($count = null, $state = [])
 * @method static Builder<static>|UserClient filterBasedOnPIC()
 * @method static Builder<static>|UserClient getMentoredStudents()
 * @method static Builder<static>|UserClient hasNoPIC()
 * @method static Builder<static>|UserClient isActive()
 * @method static Builder<static>|UserClient isActiveMentee()
 * @method static Builder<static>|UserClient isGraduated()
 * @method static Builder<static>|UserClient isNotActive()
 * @method static Builder<static>|UserClient isNotBlacklist()
 * @method static Builder<static>|UserClient isNotSalesAdmin()
 * @method static Builder<static>|UserClient isNotVerified()
 * @method static Builder<static>|UserClient isParent()
 * @method static Builder<static>|UserClient isRaw()
 * @method static Builder<static>|UserClient isStudent()
 * @method static Builder<static>|UserClient isTeacher()
 * @method static Builder<static>|UserClient isUsingAPI()
 * @method static Builder<static>|UserClient isVerified()
 * @method static Builder<static>|UserClient mentoring()
 * @method static Builder<static>|UserClient newModelQuery()
 * @method static Builder<static>|UserClient newQuery()
 * @method static Builder<static>|UserClient onlyTrashed()
 * @method static Builder<static>|UserClient query()
 * @method static Builder<static>|UserClient search($search)
 * @method static Builder<static>|UserClient whereAddress($value)
 * @method static Builder<static>|UserClient whereApplicationYear($value)
 * @method static Builder<static>|UserClient whereBlacklist($value)
 * @method static Builder<static>|UserClient whereCategory($value)
 * @method static Builder<static>|UserClient whereCity($value)
 * @method static Builder<static>|UserClient whereCreatedAt($value)
 * @method static Builder<static>|UserClient whereDeletedAt($value)
 * @method static Builder<static>|UserClient whereDob($value)
 * @method static Builder<static>|UserClient whereEdufId($value)
 * @method static Builder<static>|UserClient whereEventId($value)
 * @method static Builder<static>|UserClient whereFirstName($value)
 * @method static Builder<static>|UserClient whereGapYear($value)
 * @method static Builder<static>|UserClient whereGradeNow($value)
 * @method static Builder<static>|UserClient whereGraduatedStatus($value)
 * @method static Builder<static>|UserClient whereGraduationYear($value)
 * @method static Builder<static>|UserClient whereGraduationYearNow($value)
 * @method static Builder<static>|UserClient whereId($value)
 * @method static Builder<static>|UserClient whereInsta($value)
 * @method static Builder<static>|UserClient whereIsFunding($value)
 * @method static Builder<static>|UserClient whereIsVerified($value)
 * @method static Builder<static>|UserClient whereLastName($value)
 * @method static Builder<static>|UserClient whereLeadId($value)
 * @method static Builder<static>|UserClient whereMail($value)
 * @method static Builder<static>|UserClient whereMentoringGoogleDriveLink($value)
 * @method static Builder<static>|UserClient whereMentoringProgressStatus($value)
 * @method static Builder<static>|UserClient wherePhone($value)
 * @method static Builder<static>|UserClient wherePhoneDesc($value)
 * @method static Builder<static>|UserClient wherePostalCode($value)
 * @method static Builder<static>|UserClient wherePreferredProgram($value)
 * @method static Builder<static>|UserClient whereReferralCode($value)
 * @method static Builder<static>|UserClient whereRegisterBy($value)
 * @method static Builder<static>|UserClient whereRoleName($role)
 * @method static Builder<static>|UserClient whereSchId($value)
 * @method static Builder<static>|UserClient whereScholarship($value)
 * @method static Builder<static>|UserClient whereSecondaryId($value)
 * @method static Builder<static>|UserClient whereStAbryear($value)
 * @method static Builder<static>|UserClient whereStGrade($value)
 * @method static Builder<static>|UserClient whereStLevelinterest($value)
 * @method static Builder<static>|UserClient whereStNote($value)
 * @method static Builder<static>|UserClient whereStPassword($value)
 * @method static Builder<static>|UserClient whereStStatusact($value)
 * @method static Builder<static>|UserClient whereStStatuscli($value)
 * @method static Builder<static>|UserClient whereState($value)
 * @method static Builder<static>|UserClient whereTookIa($value)
 * @method static Builder<static>|UserClient whereTookIaDate($value)
 * @method static Builder<static>|UserClient whereUpdatedAt($value)
 * @method static Builder<static>|UserClient whereUtmContent($value)
 * @method static Builder<static>|UserClient withAndWhereHas($relation, $constraint)
 * @method static Builder<static>|UserClient withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|UserClient withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy([UserClientObserver::class])]
class UserClient extends Authenticatable
{
    use CleanStringTrait, HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'tbl_client';

    public $incrementing = false;

    protected $appends = ['lead_source', 'referral_name'];

    protected $keyType = 'string';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    // CLIENT MODEL
    protected $fillable = [
        'id',
        'secondary_id',
        'st_id',
        // 'uuid',
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
        'grade_now',
        'lead_id',
        'eduf_id',
        'partner_id',
        'event_id',
        'st_levelinterest',
        'graduation_year',
        'graduation_year_now',
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
        'register_by',
        'referral_code',
        'category',
        'utm_content',
        'took_ia',
        'took_ia_date',
        'blacklist',
        'mentoring_progress_status',
        'mentoring_google_drive_link',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_client', 'channel_datatable'));

        // Delete cache birthDay
        Cache::has('birthDay') ? Cache::forget('birthDay') : null;

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update
        $instance = new self; // Create a temporary instance

        isset($attributes['first_name']) ? $attributes['first_name'] = $instance->cleanString($attributes['first_name']) : null;
        isset($attributes['last_name']) ? $attributes['last_name'] = $instance->cleanString($attributes['last_name']) : null;

        $updated = parent::update($attributes);

        if (isset($attributes['is_many_request']) && $attributes['is_many_request']) {
            unset($attributes['is_many_request']);
        } else {
            // Send to pusher
            // Custom logic after creating the model
            event(new MessageSent('rt_client', 'channel_datatable'));
            // Delete cache birthDay
            Cache::has('birthDay') ? Cache::forget('birthDay') : null;
        }

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $instance = new self; // Create a temporary instance

        isset($attributes['first_name']) ? $attributes['first_name'] = $instance->cleanString($attributes['first_name']) : null;
        isset($attributes['last_name']) ? $attributes['last_name'] = $instance->cleanString($attributes['last_name']) : null;

        $model = static::query()->create($attributes);

        if (isset($attributes['is_many_request']) && $attributes['is_many_request']) {
            unset($attributes['is_many_request']);
        } else {
            // Send to pusher
            // Custom logic after creating the model
            event(new MessageSent('rt_client', 'channel_datatable'));

            // Delete cache birthDay
            Cache::has('birthDay') ? Cache::forget('birthDay') : null;
        }

        ProcessUpdateGradeAndGraduationYearNow::dispatch($model->id)->onQueue('update-grade-and-graduation-year-now');

        return $model;
    }

    // attributes
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($this->last_name) ? $this->first_name.' '.$this->last_name : $this->first_name,
        );
    }

    protected function leadSource(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->lead != null ? $this->getLeadSource($this->lead->main_lead) : null
        );
    }

    protected function clientProgs(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->clientProgram != null ? $this->clientProgram : null
        );
    }

    protected function listInterestCountries(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getListInterestCountries()
        );
    }

    protected function listJoinedEvents(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getListJoinedEvent()
        );
    }

    protected function listInterestProgs(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getListInterestProgs()
        );
    }

    protected function picId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getListPics()[0]
        );
    }

    protected function picName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getListPics()[1]
        );
    }

    // protected function gradeNow(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => $this->getGradeNowFromView($this->id)
    //     );
    // }

    // protected function graduationYearReal(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => $this->getGraduationYearFromView($this->id)
    //     );
    // }

    protected function participated(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getParticipatedFromView($this->id)
        );
    }

    protected function referralName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->referral_code != null ? $this->getReferralNameFromRefCodeView($this->referral_code) : null
        );
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        $terms = $search['terms'] ?? null;
        $uni = $search['uni'] ?? null;
        $major = $search['major'] ?? null;
        $mentor_type = $search['mentor_type'] ?? [];

        return $query->when($terms, function ($query) use ($terms) {
            /* previously, terms variable used in order to find name in active mentee mentoring app */
            /* but they want to search by grade and school name also */
            /* so we will add more where to cover that problem */
            $query->
                whereRaw('CONCAT(first_name, " ", last_name) like ?', ['%'.$terms.'%'])->
                orWhereRaw('grade_now like ?', ['%'.$terms.'%'])->
                orWhereHas('school', function ($query) use ($terms) {
                    $query->where('sch_name', 'like', '%'.$terms.'%');
                });
        })->when($uni, function ($query) use ($uni) {
            $query->where(function ($query) use ($uni) {
                $query->whereHas('decidedUniversityAcceptance', function ($query) use ($uni) {
                    $query->where('univ_name', 'like', '%'.$uni.'%');
                });
            });
        })->when($major, function ($query) use ($major) {
            $query->where(function ($query) use ($major) {
                $query->where(function ($query) use ($major) {
                    $query->
                    whereHas('universityAcceptance', function ($query) use ($major) {
                        $query->where('tbl_client_acceptance.major_name', 'like', '%'.$major.'%')->where('status', 'final decision');
                    })->
                    orWhereHas('majorAcceptance', function ($query) use ($major) {
                        $query->where('name', 'like', '%'.$major.'%')->where('status', 'final decision');
                    })->
                    orWhereHas('majorGroupAcceptance', function ($query) use ($major) {
                        $query->where('mg_name', 'like', '%'.$major.'%')->where('status', 'final decision');
                    });
                });
            });
        })->when($mentor_type, function ($query) {
            // ! Column not found: 1054 Unknown column 'users.id' in 'where clause'
            // $query->whereHas('clientProgram.clientMentor', function ($query) use ($mentor_type) {
            //     $query->where('users.id', auth()->guard('api')->user()->id)->whereIn('tbl_client_mentor.type', $mentor_type)->where('tbl_client_mentor.status', 1);
            // });
        });
    }

    public function scopeIsNotBlacklist($query)
    {
        return $query->where('tbl_client.blacklist', 0);
    }

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

    public function scopeIsRaw($query)
    {
        return $query->where('is_verified', 'N')->where('st_statusact', 1)->where('deleted_at', null);
    }

    public function scopeMentoring(Builder $query)
    {
        $query->whereHas('clientProgram', function ($query) {
            $query->where('status', 1)->whereHas('program.main_prog', function ($query) {
                $query->where('prog_name', 'Admissions Mentoring');
            });
        });
    }

    public function scopeIsGraduated(Builder $query)
    {
        $query->
            where(function ($query) {
                $query->where('grade_now', '>', 12)->whereDoesntHave('clientProgram', function ($query) {
                    $query->whereIn('status', [0, 2, 3, 5]);
                })->whereHas('clientProgram', function ($query) {
                    $query->whereIn('status', [1, 4]);
                });
            })->
            orWhere(function ($query) {
                $query->whereHas('clientProgram', function ($query) {
                    $query->where('prog_running_status', 2);
                })->whereDoesntHave('clientProgram', function ($query) {
                    $query->where('prog_running_status', '!=', 2);
                });
            });

    }

    public function scopeIsActiveMentee(Builder $query)
    {
        $query->
            whereRelation('clientProgram.program.main_prog', 'prog_name', 'Admissions Mentoring')->
            whereRelation('clientProgram', 'status', 1)->
            whereDoesntHave('clientProgram', function ($query) {
                $query->whereHas('program.main_prog', function ($query) {
                    $query->where('prog_name', 'Admissions Mentoring');
                })->where('prog_running_status', 2);
            });
        // whereRelation('clientProgram', 'prog_running_status', '!=', 2)
    }

    public function scopeGetMentoredStudents(Builder $query)
    {
        $query->when(auth()->guard('api')->check(), function ($query) {
            $query->whereHas('clientProgram.clientMentor', function ($query) {
                $query->where('users.id', auth()->guard('api')->user()->id)->where('tbl_client_mentor.status', 1);
            });
        });
    }

    public function getLeadSource($parameter)
    {
        switch ($parameter) {
            case 'EduALL Event':
                if ($this->event != null) {
                    return 'EduALL Event - '.$this->event->event_title;
                } else {
                    return 'EduALL Event';
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

    public function getListInterestCountries()
    {
        $listInterestCountries = [];

        if (count($this->destinationCountries) > 0) {
            foreach ($this->destinationCountries as $destinationCountry) {
                if ($destinationCountry->name == 'Other' && isset($destinationCountry->tagCountry)) {
                    $listInterestCountries[] = $destinationCountry->tagCountry->name;
                } else {
                    $listInterestCountries[] = $destinationCountry->name;
                }
            }
        }

        return implode(', ', $listInterestCountries);
    }

    public function getListJoinedEvent()
    {
        $listJoinedEvents = [];

        if (count($this->clientEvent) > 0) {
            foreach ($this->clientEvent as $clientEvent) {
                $listJoinedEvents[] = $clientEvent->event->event_title;
            }
        }

        return implode(', ', $listJoinedEvents);
    }

    public function getListInterestProgs()
    {
        $listInterestProgs = [];

        if (count($this->interestPrograms) > 0) {
            foreach ($this->interestPrograms as $interestProgram) {
                $listInterestProgs[] = $interestProgram->program_name;
            }
        }

        return implode(', ', array_unique($listInterestProgs));
    }

    public function getListPics()
    {
        // index 0 = pic->user_id, index 1 = pic_name
        $listPics[0] = null;
        $listPics[1] = null;

        if (count($this->picClient) > 0) {
            $listPics[0] = $this->picClient->where('status', 1)->first()->user_id ?? null;
            $listPics[1] = $this->picClient->where('status', 1)->first()->user->full_name ?? null;
        }

        return $listPics;
    }

    // public function getGraduationYearFromView($id)
    // {
    //     return DB::table('client')->find($id)->graduation_year_real ?? null;
    // }

    // public function getGradeNowFromView($id)
    // {
    //     return DB::table('client')->find($id)->grade_now ?? null;
    // }

    public function getParticipatedFromView($id)
    {
        return DB::table('client')->find($id)->participated;
    }

    public function getReferralNameFromRefCodeView($refCode)
    {
        return UserClient::where('secondary_id', $refCode)->first()->full_name ?? null;
    }

    // relation
    public function mentoringLogs()
    {
        return $this->hasMany(mentoringLog::class, 'student_id', 'id');
    }

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
        return $this->belongsToMany(MasterCountry::class, 'tbl_client_abrcountry', 'client_id', 'country_id')->withTimestamps();
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

    public function latestOfferedProgram()
    {
        return $this->hasOne(ClientProgram::class, 'client_id', 'id')->ofMany([
            'clientprog_id' => 'max',
        ], function ($query) {
            $query->where('status', 0); // pending
        });
    }

    public function latestAdmissionProgram()
    {
        return $this->hasOne(ClientProgram::class, 'client_id', 'id')->ofMany([
            'clientprog_id' => 'max',
        ], function ($query) {
            $query->whereHas('program.main_prog', function ($sub) {
                $sub->where('prog_name', 'Admissions Mentoring');
            })->whereIn('status', [1, 4]); // success
        });
    }

    public function latestNonAdmissionProgram()
    {
        return $this->hasOne(ClientProgram::class, 'client_id', 'id')->ofMany([
            'clientprog_id' => 'max',
        ], function ($query) {
            $query->whereHas('program.main_prog', function ($sub) {
                $sub->whereNot('prog_name', 'Admissions Mentoring');
            })->whereIn('status', [1, 4]); // success
        });
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'client_id', 'id');
    }

    public function clientMentor()
    {
        return $this->hasManyThrough(
            ClientMentor::class,
            ClientProgram::class,
            'client_id',
            'clientprog_id',
            'id',
            'clientprog_id'
        );
        // return $this->hasManyThrough(User::class, ClientProgram::class, 'client_id', 'users.id', 'id', 'clientprog_id');
    }

    public function leadStatus()
    {
        return $this->belongsToMany(InitialProgram::class, 'tbl_client_lead_tracking', 'client_id', 'initialprogram_id')->using(ClientLeadTracking::class)->withPivot('type', 'total_result', 'status')->withTimestamps();
    }

    public function majorAcceptance()
    {
        return $this->belongsToMany(Major::class, 'tbl_client_acceptance', 'client_id', 'major_id');
    }

    public function majorGroupAcceptance()
    {
        return $this->belongsToMany(MajorGroup::class, 'tbl_client_acceptance', 'client_id', 'major_group_id')->withTimestamps();
    }

    public function universityAcceptance()
    {
        return $this->belongsToMany(University::class, 'tbl_client_acceptance', 'client_id', 'univ_id')->using(ClientAcceptance::class)->withPivot('id', 'major_group_id', 'major_name', 'status', 'major_id', 'category', 'requirement_link')->withTimestamps();
    }

    public function decidedUniversityAcceptance()
    {
        return $this->belongsToMany(University::class, 'tbl_client_acceptance', 'client_id', 'univ_id')->using(ClientAcceptance::class)->withPivot('id', 'major_group_id', 'major_name', 'status', 'major_id', 'category', 'requirement_link')->wherePivot('status', 'Final Decision')->withTimestamps();
    }

    public function picClient()
    {
        return $this->hasMany(PicClient::class, 'client_id', 'id');
    }

    public function viewClientRefCode()
    {
        return $this->belongsTo(ViewClientRefCode::class, 'id', 'id');
    }

    // PIC from sales team
    public function handledBy()
    {
        return $this->belongsToMany(User::class, 'tbl_pic_client', 'client_id', 'user_id')->withPivot('id', 'status');
    }

    public function followupSchedule()
    {
        return $this->hasMany(FollowupClient::class, 'client_id', 'id');
    }

    public function client_log()
    {
        return $this->hasMany(ClientLog::class, 'client_id', 'id');
    }
}
