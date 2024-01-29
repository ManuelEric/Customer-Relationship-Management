<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\HasApiTokens;
use Mostafaznv\LaraCache\CacheEntity;
use Mostafaznv\LaraCache\Traits\LaraCache;

class Client extends Model
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $table = 'client';

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
        'register_as',
        'is_verified',
        'created_at',
        'updated_at',
        'deleted_at',
        'pic_id',
        'status_lead',
        'status_lead_score',
    ];

    # attributes
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($this->last_name) ? $this->first_name . ' ' . $this->last_name : $this->first_name,
        );
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    # Scopes
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

    public function scopeIsUsingAPI($query)
    {
        return $query->when(auth()->guard('api')->user(), function ($subQuery) {
            $subQuery->whereHas('handledBy', function ($subQuery_2) {
                $subQuery_2->where('users.id', auth()->guard('api')->user()->id);
            });
        });
    }

    # attributes
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('d M Y H:i:s', strtotime($value)),
        );
    }

    protected function showGrade(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->grade_now > 12 ? "Not high school" : $this->grade_now
        );
    }

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
        return $this->belongsToMany(Tag::class, 'tbl_client_abrcountry', 'client_id', 'tag_id');
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

    # PIC from sales team
    public function handledBy()
    {
        return $this->belongsToMany(User::class, 'tbl_pic_client', 'client_id', 'user_id');
    }
}
