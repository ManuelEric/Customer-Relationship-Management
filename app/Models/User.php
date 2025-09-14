<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Events\MessageSent;
use App\Models\pivot\AgendaSpeaker;
use App\Models\pivot\AssetUsed;
use App\Models\pivot\EditorAgreement;
use App\Models\pivot\UserRole;
use App\Models\pivot\UserStream;
use App\Models\pivot\UserSubject;
use App\Models\pivot\UserTypeDetail;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

#[ObservedBy([UserObserver::class])]
/**
 * @property int $number
 * @property string $id
 * @property string|null $nip
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $address
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $phone
 * @property int|null $emergency_contact_phone
 * @property string|null $emergency_contact_relation_name
 * @property string|null $datebirth
 * @property int|null $position_id
 * @property string|null $password
 * @property string|null $hiredate
 * @property int|null $nik
 * @property string|null $idcard
 * @property string|null $cv
 * @property string|null $bank_name
 * @property string|null $account_name
 * @property string|null $account_no
 * @property string|null $npwp
 * @property string|null $tax
 * @property int $active
 * @property string|null $health_insurance
 * @property string|null $empl_insurance
 * @property int $export
 * @property string|null $notes
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Menu> $access_menus
 * @property-read int|null $access_menus_count
 * @property-read UserTypeDetail|UserRole|AssetUsed|AgendaSpeaker|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $as_event_speaker
 * @property-read int|null $as_event_speaker_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SchoolProg> $as_schoolprog_speaker
 * @property-read int|null $as_schoolprog_speaker_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assetUsed
 * @property-read int|null $asset_used_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientProgram> $clientProgram
 * @property-read int|null $client_program_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Department> $department
 * @property-read int|null $department_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EditorAgreement> $editor_agreement
 * @property-read int|null $editor_agreement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\University> $educations
 * @property-read int|null $educations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EdufReview> $edufairReview
 * @property-read int|null $edufair_review_count
 * @property-read mixed $encrypted_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FollowupClient> $followupSchedule
 * @property-read int|null $followup_schedule_count
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $handle
 * @property-read int|null $handle_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $handledEvent
 * @property-read int|null $handled_event_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientProgram> $mentorClient
 * @property-read int|null $mentor_client_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SchoolVisit> $pic_school_visit
 * @property-read int|null $pic_school_visit_count
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserStream> $user_streams
 * @property-read int|null $user_streams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserSubject> $user_subjects
 * @property-read int|null $user_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserType> $user_type
 * @property-read int|null $user_type_count
 *
 * @method static Builder<static>|User active()
 * @method static Builder<static>|User department(string $department)
 * @method static Builder<static>|User editor()
 * @method static Builder<static>|User externalMentor()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User hasDepartment($department)
 * @method static Builder<static>|User hasRole($role)
 * @method static Builder<static>|User internship($expected_end_date)
 * @method static Builder<static>|User isActive()
 * @method static Builder<static>|User isAdminSales()
 * @method static Builder<static>|User isPIC()
 * @method static Builder<static>|User isSales()
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User partTime($expected_end_date)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User role(string $role)
 * @method static Builder<static>|User tutor()
 * @method static Builder<static>|User whereAccountName($value)
 * @method static Builder<static>|User whereAccountNo($value)
 * @method static Builder<static>|User whereActive($value)
 * @method static Builder<static>|User whereAddress($value)
 * @method static Builder<static>|User whereBankName($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereCv($value)
 * @method static Builder<static>|User whereDatebirth($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereEmergencyContactPhone($value)
 * @method static Builder<static>|User whereEmergencyContactRelationName($value)
 * @method static Builder<static>|User whereEmplInsurance($value)
 * @method static Builder<static>|User whereExport($value)
 * @method static Builder<static>|User whereFirstName($value)
 * @method static Builder<static>|User whereHealthInsurance($value)
 * @method static Builder<static>|User whereHiredate($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereIdcard($value)
 * @method static Builder<static>|User whereLastName($value)
 * @method static Builder<static>|User whereNik($value)
 * @method static Builder<static>|User whereNip($value)
 * @method static Builder<static>|User whereNotes($value)
 * @method static Builder<static>|User whereNpwp($value)
 * @method static Builder<static>|User whereNumber($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePhone($value)
 * @method static Builder<static>|User wherePositionId($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereTax($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withAndWhereHas($relation, $constraint)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<int, string>
     */
    protected $fillable = [
        'number',
        'id',
        'nip',
        'first_name',
        'last_name',
        'address',
        'email',
        'phone',
        'emergency_contact_phone',
        'emergency_contact_relation_name',
        'datebirth',
        'position_id',
        'password',
        'hiredate',
        'nik',
        'idcard',
        'cv',
        'bank_name',
        'account_name',
        'account_no',
        'npwp',
        'tax',
        'active',
        'health_insurance',
        'empl_insurance',
        'export',
        'notes',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_user', 'channel_datatable'));

        // Delete Cache menu
        Cache::has('menu') ? Cache::forget('menu') : null;

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_user', 'channel_datatable'));

        // Delete Cache menu
        Cache::has('menu') ? Cache::forget('menu') : null;

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_user', 'channel_datatable'));

        // Delete Cache menu
        Cache::has('menu') ? Cache::forget('menu') : null;

        return $model;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public static function whereFullName($name)
    {
        if (is_array($name) && empty($name)) {
            return new Collection;
        }

        $instance = new static;

        return $instance->newQuery()->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%'.$name.'%'])->first();
    }

    protected function encryptedId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Crypt::encrypt($this->id)
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->first_name.' '.$this->last_name,
        );
    }

    /**
     * The scopes.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function scopeRole(Builder $query, string $role): void
    {
        $query->whereHas('roles', function ($sub) use ($role) {
            $sub->where('role_name', $role);
        });
    }

    public function scopeDepartment(Builder $query, string $department): void
    {
        $query->whereHas('departments', function ($sub) use ($department) {
            $sub->where('dept_name', 'like', '%'.$department.'%');
        });
    }

    public function scopeTutor(Builder $query): void
    {
        $query->whereHas('roles', function ($sub) {
            $sub->where('role_name', 'Tutor');
        });
    }

    public function scopeEditor(Builder $query): void
    {
        $query->whereHas('roles', function ($sub) {
            $sub->where('role_name', 'like', '%Editor');
        });
    }

    public function scopeExternalMentor(Builder $query): void
    {
        $query->whereHas('roles', function ($sub) {
            $sub->where('role_name', 'Mentor');
        })->whereDoesntHave('roles', function ($sub) {
            $sub->where('role_name', 'Employee');
        });
    }

    public function scopeInternship(Builder $query, $expected_end_date): void
    {
        $query->whereHas('user_type', function ($sub) use ($expected_end_date) {
            $sub->where('tbl_user_type_detail.status', 1)-> // dimana status contractnya active
                where('tbl_user_type_detail.end_date', $expected_end_date)-> // dimana end date nya sudah H-2 weeks
                where('tbl_user_type.type_name', 'Internship');
        });
    }

    public function scopePartTime(Builder $query, $expected_end_date): void
    {
        $query->whereHas('user_type', function ($sub) use ($expected_end_date) {
            $sub->where('tbl_user_type_detail.status', 1)-> // dimana status contractnya active
                where('tbl_user_type_detail.end_date', $expected_end_date)-> // dimana end date nya sudah H-2 weeks
                where('tbl_user_type.type_name', 'Part-Time');
        });
    }

    public function scopeIsActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    public function scopeIsAdminSales($query)
    {
        return $query->whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Admin');
        })->whereHas('departments', function ($subQuery) {
            $subQuery->where('dept_name', 'Client Management')->where('tbl_user_type_detail.status', 1);
        })->count() > 0 ? true : false;
    }

    public function scopeIsPIC($query)
    {
        return $query->whereDoesntHave('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Admin');
        })->whereHas('departments', function ($subQuery) {
            $subQuery->where('dept_name', 'Client Management')->where('tbl_user_type_detail.status', 1);
        })->get();
    }

    public function scopeIsSales($query)
    {
        return $query->whereHas('departments', function ($subQuery) {
            $subQuery->where('dept_name', 'Client Management')->where('tbl_user_type_detail.status', 1);
        });
    }

    public function scopeHasRole($query, $role)
    {
        return $query->whereHas('roles', function ($subQuery) use ($role) {
            $subQuery->where('role_name', $role);
        })->exists();
    }

    public function scopeHasDepartment($query, $department)
    {
        return $this->belongsToMany(Role::class, 'tbl_user_roles', 'user_id', 'role_id')->using(UserRole::class)->withTimestamps()->withPivot('id', 'user_id', 'role_id', 'extended_id');
    }

    /**
     * The relations.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_user_roles', 'user_id', 'role_id')->using(UserRole::class)->withTimestamps()->withPivot('id', 'user_id', 'role_id', 'capacity');
    }

    public function departments()
    {
        Cache::has('menu') ? Cache::forget('menu') : null;

        return $this->belongsToMany(Department::class, 'tbl_user_type_detail', 'user_id', 'department_id')->withPivot(
            [
                'user_type_id',
                'user_id',
                'department_id',
                'start_date',
                'end_date',
                'status',
            ]
        )->withTimestamps();
    }

    public function access_menus()
    {
        Cache::has('menu') ? Cache::forget('menu') : null;

        return $this->belongsToMany(Menu::class, 'tbl_menus_user', 'user_id', 'menu_id')->withPivot(['copy', 'export'])->withTimestamps();
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    public function educations()
    {
        return $this->belongsToMany(University::class, 'tbl_user_educations', 'user_id', 'univ_id')
            ->withPivot('id', 'major_id', 'tbl_major.name as major_name', 'degree', 'graduation_date')->withTimestamps()
            ->join('tbl_major', 'major_id', '=', 'tbl_major.id');
    }

    public function assetUsed()
    {
        return $this->belongsToMany(Asset::class, 'tbl_asset_used', 'user_id', 'asset_id')->using(AssetUsed::class)->withPivot(
            [
                'id',
                'used_date',
                'amount_used',
                'condition',
            ]
        );
    }

    public function edufairReview()
    {
        return $this->hasMany(EdufReview::class, 'reviewer_name', 'id');
    }

    public function handledEvent()
    {
        return $this->belongsToMany(Event::class, 'tbl_event_pic', 'empl_id', 'event_id');
    }

    public function as_event_speaker()
    {
        return $this->belongsToMany(Event::class, 'tbl_agenda_speaker', 'empl_id', 'event_id')->using(AgendaSpeaker::class);
    }

    public function as_schoolprog_speaker()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_agenda_speaker', 'empl_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'empl_id', 'id');
    }

    public function mentorClient()
    {
        return $this->belongsToMany(ClientProgram::class, 'tbl_client_mentor', 'user_id', 'clientprog_id')->withTimestamps();
    }

    public function user_type()
    {
        Cache::has('menu') ? Cache::forget('menu') : null;

        return $this->belongsToMany(UserType::class, 'tbl_user_type_detail', 'user_id', 'user_type_id')->using(UserTypeDetail::class)->withPivot('id', 'department_id', 'start_date', 'end_date', 'status', 'deactivated_at')->withTimestamps();
    }

    public function pic_school_visit()
    {
        return $this->hasMany(SchoolVisit::class, 'internal_pic', 'id');
    }

    public function user_subjects()
    {
        return $this->hasManyThrough(UserSubject::class, UserRole::class, 'user_id', 'user_role_id', 'id', 'id')->with('subject', 'user_roles');
    }

    public function user_streams()
    {
        return $this->hasManyThrough(UserStream::class, UserRole::class, 'user_id', 'user_role_id', 'id', 'id')->with('stream', 'user_roles');
    }

    public function editor_agreement()
    {
        return $this->hasManyThrough(EditorAgreement::class, UserRole::class, 'user_id', 'user_role_id', 'id', 'id')->with('user_roles');
    }

    // applied when user from sales department
    public function handle()
    {
        return $this->belongsToMany(Client::class, 'tbl_pic_client', 'user_id', 'client_id');
    }

    public function followupSchedule()
    {
        return $this->hasMany(FollowupClient::class, 'user_id', 'id');
    }
}
