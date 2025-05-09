<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Jobs\Client\ProcessUpdateGradeAndGraduationYearNow;
use App\Models\pivot\ClientAcceptance;
use App\Models\pivot\ClientLeadTracking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class UserClient extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'tbl_client';
    public $incrementing = false;
    protected $appends = ['lead_source', 'referral_name'];
    protected $keyType = 'string';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
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

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(New MessageSent('rt_client', 'channel_datatable'));

        // Delete cache birthDay
        Cache::has('birthDay') ? Cache::forget('birthDay') : null;

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
            // Send to pusher
            // Custom logic after creating the model
            event(New MessageSent('rt_client', 'channel_datatable'));
            // Delete cache birthDay
            Cache::has('birthDay') ? Cache::forget('birthDay') : null;
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
            // Send to pusher
            // Custom logic after creating the model
            event(New MessageSent('rt_client', 'channel_datatable'));

            // Delete cache birthDay
            Cache::has('birthDay') ? Cache::forget('birthDay') : null;
        }

        ProcessUpdateGradeAndGraduationYearNow::dispatch($model->id)->onQueue('update-grade-and-graduation-year-now');

        return $model;
    }

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
            get: fn ($value) => $this->referral_code != NULL ? $this->getReferralNameFromRefCodeView($this->referral_code) : NULL
        );
    }

    # Scopes
    public function scopeSearch($query, $search)
    {
        $terms = $search['terms'];
        $uni = $search['uni'] ?? null;
        $major = $search['major'] ?? null;

        return $query->
            when($terms, function ($query) use ($terms) {
                $query->whereRaw('CONCAT(first_name, " ", last_name) like "%'.$terms.'%"');
            })->
            when($uni, function ($query) use ($uni) {
                $query->
                where(function ($query) use ($uni) {
                    $query->
                        whereRelation('universityAcceptance', 'tbl_client_acceptance.status', 'final decision')->
                        whereHas('universityAcceptance', function ($query) use ($uni) {
                            $query->where('univ_name', 'like', '%'.$uni.'%');
                        });
                });
            })->
            when($major, function ($query) use ($major) {
                $query->
                where(function ($query) use ($major) {
                    $query->
                        whereRelation('universityAcceptance', 'tbl_client_acceptance.status', 'final decision')->
                        where(function ($query) use ($major) {
                            $query->
                            whereHas('universityAcceptance', function ($query) use ($major) {
                                $query->where('tbl_client_acceptance.major_name', 'like', '%'.$major.'%');
                            })->
                            orWhereHas('majorAcceptance', function ($query) use ($major) {
                                $query->where('name', 'like', '%'.$major.'%');
                            });
                        });
                });
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

    public function scopeIsGraduated(Builder $query)
    {
        $query->
        where('grade_now', '>', 12)->
        whereDoesntHave('clientProgram', function ($query) {
            $query->whereIn('status', [0, 2, 3, 5]);
        })->
        whereHas('clientProgram', function ($query) {
            $query->whereIn('status', [1, 4]);
        });
    }

    public function scopeIsActiveMentee(Builder $query)
    {
        $query->whereRelation('clientProgram.program.main_prog', 'prog_name', 'Admissions Mentoring')->whereRelation('clientProgram', 'status', 1)->whereRelation('clientProgram', 'prog_running_status', '!=', 2);
    }

    public function scopeGetMentoredStudents(Builder $query)
    {
        $query->whereHas('clientProgram.clientMentor', function ($query) {
            $query->where('users.id', auth()->guard('api')->user()->id)->where('tbl_client_mentor.status', 1);
        });
    }



    public function getLeadSource($parameter)
    {
        switch ($parameter) {
            case "EduALL Event":
                if ($this->event != NULL)
                    return "EduALL Event - " . $this->event->event_title;
                else
                    return "EduALL Event";
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
    public function getListInterestCountries()
    {
        $listInterestCountries = [];

        if(count($this->destinationCountries) > 0){
            foreach ($this->destinationCountries as $destinationCountry) {
                if($destinationCountry->name == 'Other' && isset($destinationCountry->tagCountry)){
                    $listInterestCountries[] = $destinationCountry->tagCountry->name;
                }else{
                    $listInterestCountries[] = $destinationCountry->name;
                }
            }
        }

        return implode(", ",$listInterestCountries);
    }
    public function getListJoinedEvent()
    {
        $listJoinedEvents = [];

        if(count($this->clientEvent) > 0){
            foreach ($this->clientEvent as $clientEvent) {
                $listJoinedEvents[] = $clientEvent->event->event_title;
            }
        }

        return implode(", ",$listJoinedEvents);
    }
    public function getListInterestProgs()
    {
        $listInterestProgs = [];

        if(count($this->interestPrograms) > 0){
            foreach ($this->interestPrograms as $interestProgram) {
                $listInterestProgs[] = $interestProgram->program_name;
            }
        }

        return implode(", ",array_unique($listInterestProgs));
    }
    public function getListPics()
    {
        # index 0 = pic->user_id, index 1 = pic_name
        $listPics[0] = null;
        $listPics[1] = null;

        if(count($this->picClient) > 0){
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
            $query->where('status', 0); # pending
        });
    }

    public function latestAdmissionProgram()
    {
        return $this->hasOne(ClientProgram::class, 'client_id', 'id')->ofMany([
            'clientprog_id' => 'max',
        ], function ($query) {
            $query->
            whereHas('program.main_prog', function ($sub) {
                $sub->where('prog_name', 'Admissions Mentoring');
            })->
            whereIn('status', [1, 4]); # success
        });
    }

    public function latestNonAdmissionProgram()
    {
        return $this->hasOne(ClientProgram::class, 'client_id', 'id')->ofMany([
            'clientprog_id' => 'max',
        ], function ($query) {
            $query->
            whereHas('program.main_prog', function ($sub) {
                $sub->whereNot('prog_name', 'Admissions Mentoring');
            })->
            whereIn('status', [1, 4]); # success
        });
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

    public function majorAcceptance()
    {
        return $this->belongsToMany(Major::class, 'tbl_client_acceptance', 'client_id', 'major_id');
    }

    public function universityAcceptance()
    {
        return $this->belongsToMany(University::class, 'tbl_client_acceptance', 'client_id', 'univ_id')->using(ClientAcceptance::class)->withPivot('id', 'major_group_id', 'major_name', 'status', 'major_id', 'category', 'requirement_link')->withTimestamps();
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

    public function client_log()
    {
        return $this->hasMany(ClientLog::class, 'client_id', 'id');
    }
}
