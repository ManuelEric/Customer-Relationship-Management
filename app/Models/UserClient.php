<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserClient extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_client';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'st_id',
        'first_name',
        'last_name',
        'mail',
        'phone',
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
        'event_id',
        'st_levelinterest',
        'graduation_year',
        'st_abryear',
        // 'st_abrcountry',
        'st_statusact',
        'st_note',
        'st_prospect_status',
        'password',
    ];

    # attributes
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->first_name.' '.$this->last_name,
        );
    }

    protected function leadSource(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getLeadSource($this->lead->main_lead)
        );
    }

    public function getLeadSource($parameter)
    {
        switch ($parameter) {
            case "All-In Event":
                return "ALL-In Event - ".$this->event->event_title;
                break;

            case "External Edufair":
                return "External Edufair - ".$this->external_edufair->title;
                break;

            case "KOL":
                return "KOL - ".$this->lead->sub_lead;
                break;

            default:
                return $this->lead->main_lead;
        }
    }

    # relation
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
        return $this->belongsToMany(Tag::class, 'tbl_client_abrcountry', 'client_id', 'tag_id')->withTimestamps();
    }

    public function interestUniversities()
    {
        return $this->belongsToMany(University::class, 'tbl_dreams_uni', 'client_id', 'univ_id')->withTimestamps();
    }

    public function interestPrograms()
    {
        return $this->belongsToMany(Program::class, 'tbl_interest_prog', 'client_id', 'prog_id')->withTimestamps();
    }

    public function interestMajor()
    {
        return $this->belongsToMany(Major::class, 'tbl_dreams_major', 'client_id', 'major_id')->withTimestamps();
    }
}
