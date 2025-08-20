<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $clientprog_id
 * @property string $followup_date
 * @property int $status 0: Not yet, 1: Done
 * @property string|null $notes
 * @property int $reminder
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClientProgram $clientProgram
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp whereClientprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp whereFollowupDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp whereReminder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FollowUp whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class FollowUp extends Model
{
    use HasFactory;

    protected $table = 'tbl_followup';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'clientprog_id',
        'followup_date',
        'status',
        'notes',
        'reminder',
    ];

    // Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Delete cache follow up,
        Cache::has('followUp') ? Cache::forget('followUp') : null;

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Delete cache follow up,
        Cache::has('followUp') ? Cache::forget('followUp') : null;

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model
        // Delete cache follow up,
        Cache::has('followUp') ? Cache::forget('followUp') : null;

        return $model;
    }

    public function clientProgram()
    {
        return $this->belongsTo(ClientProgram::class, 'clientprog_id', 'clientprog_id');
    }
}
