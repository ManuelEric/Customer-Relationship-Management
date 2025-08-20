<?php

namespace App\Models;

use App\Models\pivot\UserTypeDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 * @property-read UserTypeDetail|null $user_type
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginLog query()
 *
 * @mixin \Eloquent
 */
class LoginLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_login_log';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_type_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'tbl_login_log', 'user_type_id', 'user_id');
    }

    public function user_type()
    {
        return $this->belongsTo(UserTypeDetail::class, 'user_type_id', 'id');
    }
}
