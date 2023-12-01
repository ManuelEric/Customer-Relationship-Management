<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class RawClient extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_raw_client';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'uuid',
        'fullname',
        'mail',
        'phone',
        'register_as',
        'role',
        'relation_key',
        'sch_id',
        'interest_countries',
        'lead_id',
        'graduation_year',
        'created_at',
        'updated_at',
    ];

    # attributes
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($this->last_name) ? $this->first_name . ' ' . $this->last_name : $this->first_name,
        );
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_client_roles', 'client_id', 'role_id');
    }

}
