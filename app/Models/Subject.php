<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Models\pivot\UserSubject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $role
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read UserSubject|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 *
 * @method static Builder<static>|Subject active()
 * @method static Builder<static>|Subject newModelQuery()
 * @method static Builder<static>|Subject newQuery()
 * @method static Builder<static>|Subject query()
 * @method static Builder<static>|Subject whereCreatedAt($value)
 * @method static Builder<static>|Subject whereId($value)
 * @method static Builder<static>|Subject whereIsActive($value)
 * @method static Builder<static>|Subject whereName($value)
 * @method static Builder<static>|Subject whereRole($value)
 * @method static Builder<static>|Subject whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Subject extends Model
{
    use HasFactory;

    protected $table = 'tbl_subjects';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'is_active',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_subject', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_subject', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_subject', 'channel_datatable'));

        return $model;
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function scopeActive(Builder $query)
    {
        $query->where('is_active', 1);
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'tbl_user_subjects', 'subject_id', 'user_id')->using(UserSubject::class)->withPivot('feehours', 'feesession')->withTimestamps();
    }
}
