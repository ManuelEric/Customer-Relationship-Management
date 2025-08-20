<?php

namespace App\Models;

use App\Models\pivot\UserTypeDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $type_name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read UserTypeDetail|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserType active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserType whereTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UserType extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_type';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type_name',
        'status',
    ];

    /**
     * The scopes.
     */
    public function scopeActive($query)
    {
        $query->where('status', 1);
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'tbl_user_type_detail', 'user_type_id', 'user_id')->using(UserTypeDetail::class);
    }
}
