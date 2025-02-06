<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ViewRawClient extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'raw_client';
    protected $keyType = 'string';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'fullname',
        'fname',
        'mname',
        'lname',
        'mail',
        'phone',
        'second_client_name',
        'second_client_mail',
        'second_client_phone',
        'school',
        'register_by',
        'sch_id',
        'interest_countries',
        'lead_source',
        'graduation_year_now',
        'grade_now',
        'created_at',
        'updated_at',
        'roles',
        'lead_id',
        'scholarship',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_client_roles', 'client_id', 'role_id');
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'client_id', 'id');
    }

    public function clientEventAsChild()
    {
        return $this->hasMany(ClientEvent::class, 'child_id', 'id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'client_id', 'id');
    }

    public function destinationCountries()
    {
        return $this->belongsToMany(Tag::class, 'tbl_client_abrcountry', 'client_id', 'country_id')->withTimestamps();
    }
}
