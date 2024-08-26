<?php

namespace App\Models\pivot;

use App\Models\Department;
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
        'extended_id', 
    ];

    // public function department()
    // {
    //     return $this->belongsTo(Department::class, 'department_id', 'id');
    // }
}
