<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read mixed $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\School|null $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawClient onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawClient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawClient withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RawClient withoutTrashed()
 *
 * @mixin \Eloquent
 */
class RawClient extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'tbl_raw_client';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'uuid',
        'fullname',
        'mail',
        'phone',
        'register_by',
        'role',
        'relation_key',
        'sch_id',
        'interest_countries',
        'lead_id',
        'graduation_year',
        'created_at',
        'updated_at',
    ];

    // attributes
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset($this->last_name) ? $this->first_name.' '.$this->last_name : $this->first_name,
        );
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'sch_id', 'sch_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'tbl_client_roles', 'client_id', 'role_id');
    }
}
