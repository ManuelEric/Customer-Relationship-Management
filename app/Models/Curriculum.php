<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\School> $school
 * @property-read int|null $school_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curriculum whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'tbl_curriculum';

    protected $primaryKey = 'id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_curriculum', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_curriculum', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_curriculum', 'channel_datatable'));

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

    public function school()
    {
        return $this->belongsToMany(School::class, 'tbl_sch_curriculum', 'curriculum_id', 'sch_id');
    }
}
