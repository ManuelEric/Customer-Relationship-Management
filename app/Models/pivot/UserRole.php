<?php

namespace App\Models\pivot;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    protected $table = 'tbl_user_roles';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'role_id',
    ];

    // public function department()
    // {
    //     return $this->belongsTo(Department::class, 'department_id', 'id');
    // }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subjects()
    {
        return $this->hasMany(UserSubject::class, 'user_role_id');
    }
}
