<?php

namespace App\Models;

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

    public function has_user()
    {
        return $this->belongsToMany(User::class, 'tbl_user_roles', 'role_id', 'user_id');
    }
}
