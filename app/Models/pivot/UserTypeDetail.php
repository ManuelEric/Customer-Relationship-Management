<?php

namespace App\Models\pivot;

use App\Models\LoginLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use PDO;

class UserTypeDetail extends Pivot
{

    protected $table = 'tbl_user_type_detail';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'user_type_id',
        'user_id',
        'department_id',
        'start_date',
        'end_date',
        'status',
        'deactivated_at',
    ];

    /**
     * The scopes.
     */
    public function scopeIsActive(Builder $query): void
    {
        $query->where('status', 1);
    }



    /**
     * The relations.
     */
    public function login_log()
    {
        return $this->hasMany(LoginLog::class, 'user_type_id', 'id');
    }
}
