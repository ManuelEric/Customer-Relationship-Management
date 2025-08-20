<?php

namespace App\Models\pivot;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $clientprog_id
 * @property string $user_id
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorIC newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorIC newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorIC query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorIC whereClientprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorIC whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorIC whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorIC whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorIC whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorIC whereUserId($value)
 *
 * @mixin \Eloquent
 */
class MentorIC extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_mentor_ic';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'clientprog_id',
        'user_id',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
