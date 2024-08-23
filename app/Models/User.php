<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Events\MessageSent;
use App\Models\pivot\AgendaSpeaker;
use App\Models\pivot\AssetReturned;
use App\Models\pivot\AssetUsed;
use App\Models\pivot\UserRole;
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
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
        'bankname',
        'bankacc',
        'npwp',
        'tax',
        'active',
        'health_insurance',
        'empl_insurance',
        'export',
        'notes',
    ];

    # Modify methods Model
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
        if (is_array($name) && empty($name)) return new Collection();

        $instance = new static;

        return $instance->newQuery()->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $name . '%'])->first();
    }

    protected function encryptedId(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Crypt::encrypt($this->id)
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->first_name . ' ' . $this->last_name,
        );
    }


    /**
     * The scopes.
     */
    public function scopeIsActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint){
        return $query->whereHas($relation, $constraint)
                     ->with([$relation => $constraint]);
    }

    public function scopeIsAdminSales($query)
    {
        return $query->whereHas('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Admin');
        })->whereHas('department', function ($subQuery) {
            $subQuery->where('dept_name', 'Client Management')->where('tbl_user_type_detail.status', 1);
        })->count() > 0 ? true : false;
    }

    public function scopeIsPIC($query)
    {
        return $query->whereDoesntHave('roles', function ($subQuery) {
            $subQuery->where('role_name', 'Admin');
        })->whereHas('department', function ($subQuery) {
            $subQuery->where('dept_name', 'Client Management')->where('tbl_user_type_detail.status', 1);
        })->get();
    }

    public function scopeIsSales($query)
    {
        return $query->whereHas('department', function ($subQuery) {
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
        return $query->whereHas('department', function ($subQuery) {
            $subQuery->where('tbl_user_type_detail.status', 1);
        })->exists();
    }


    /**
     * The relations.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_user_roles', 'user_id', 'role_id')->using(UserRole::class)->withTimestamps();
    }

    public function department()
    {
        Cache::has('menu') ? Cache::forget('menu') : null;

        return $this->belongsToMany(Department::class, 'tbl_user_type_detail', 'user_id', 'department_id')->withPivot(
            [
                'user_type_id',
                'user_id',
                'department_id',
                'start_date',
                'end_date',
                'status'
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
            ->withPivot('major_id', 'tbl_major.name as major_name', 'degree', 'graduation_date')->withTimestamps()
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
        return $this->hasManyThrough(UserSubject::class, UserRole::class, 'user_id', 'user_role_id', 'id', 'id');
    }

    # applied when user from sales department
    public function handle()
    {
        return $this->belongsToMany(Client::class, 'tbl_pic_client', 'user_id', 'client_id');
    }

    public function followupSchedule()
    {
        return $this->hasMany(FollowupClient::class, 'user_id', 'id');   
    }

}
