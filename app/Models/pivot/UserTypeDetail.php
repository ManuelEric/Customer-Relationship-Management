<?php

namespace App\Models\pivot;

use App\Models\LoginLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $user_type_id
 * @property string $user_id
 * @property int|null $department_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deactivated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoginLog> $login_log
 * @property-read int|null $login_log_count
 *
 * @method static Builder<static>|UserTypeDetail isActive()
 * @method static Builder<static>|UserTypeDetail newModelQuery()
 * @method static Builder<static>|UserTypeDetail newQuery()
 * @method static Builder<static>|UserTypeDetail query()
 * @method static Builder<static>|UserTypeDetail whereCreatedAt($value)
 * @method static Builder<static>|UserTypeDetail whereDeactivatedAt($value)
 * @method static Builder<static>|UserTypeDetail whereDepartmentId($value)
 * @method static Builder<static>|UserTypeDetail whereEndDate($value)
 * @method static Builder<static>|UserTypeDetail whereId($value)
 * @method static Builder<static>|UserTypeDetail whereStartDate($value)
 * @method static Builder<static>|UserTypeDetail whereStatus($value)
 * @method static Builder<static>|UserTypeDetail whereUpdatedAt($value)
 * @method static Builder<static>|UserTypeDetail whereUserId($value)
 * @method static Builder<static>|UserTypeDetail whereUserTypeId($value)
 *
 * @mixin \Eloquent
 */
class UserTypeDetail extends Pivot
{
    protected $table = 'tbl_user_type_detail';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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
