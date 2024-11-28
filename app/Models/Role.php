<?php

namespace App\Models;

use App\Http\Traits\UuidTrait;
use App\Models\pivot\UserRole;
use App\Models\pivot\UserSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Role extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'tbl_roles';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'role_name',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'tbl_user_roles', 'role_id', 'user_id')->using(UserRole::class)->withTimestamps();
    }

    public function client()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_roles', 'role_id', 'client_id')->using(new class extends Pivot {
            use UuidTrait;
        });
    }

    public function subjects()
    {
        return $this->hasManyThrough(UserSubject::class, UserRole::class, 'role_id', 'user_role_id', 'id', 'id');
    }
}
