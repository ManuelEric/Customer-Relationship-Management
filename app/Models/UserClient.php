<?php

namespace App\Models;

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
        'address',
        'sch_id',
        'st_grade',
        'lead_id',
        'eduf_id',
        'st_levelinterest',
        'prog_id',
        'graduation_year',
        'st_abryear',
        'st_abrcountry',
        'st_statusact',
        'st_note',
        'st_statuscli',
        'st_password',
    ];

    # relation
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_client_roles', 'client_id', 'role_id');
    }
}
