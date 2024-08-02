<?php

namespace App\Models;

use App\Models\pivot\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

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
        return $this->belongsToMany(UserClient::class, 'tbl_client_roles', 'role_id', 'client_id');
    }
}
