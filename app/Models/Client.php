<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

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
}
