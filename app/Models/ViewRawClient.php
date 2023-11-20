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
        'school_uuid',
        'interest_countries',
        'lead_id',
        'graduation_year',
        'created_at',
        'updated_at',
    ];

}
