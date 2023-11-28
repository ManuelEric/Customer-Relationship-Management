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
        'fullname',
        'mail',
        'phone',
        'parent_uuid',
        'parent_name',
        'parent_mail',
        'parent_phone',
        'school',
        'register_as',
        'role',
        'relation_key',
        'sch_id',
        'interest_countries',
        'lead_source',
        'graduation_year',
        'created_at',
        'updated_at',
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

}
