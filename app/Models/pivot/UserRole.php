<?php

namespace App\Models\pivot;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $user_id
 * @property int $role_id
 * @property int|null $capacity used for mentors to determine how many mentee's he/she can handle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\pivot\UserStream> $streams
 * @property-read int|null $streams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\pivot\UserSubject> $subjects
 * @property-read int|null $subjects_count
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserRole extends Pivot
{
    protected $table = 'tbl_user_roles';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'role_id',
        'capacity', // used for mentor
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

    public function streams()
    {
        return $this->hasMany(UserStream::class, 'user_role_id');
    }
}
