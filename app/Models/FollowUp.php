<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FollowUp extends Model
{
    use HasFactory;

    protected $table = 'tbl_followup';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'clientprog_id',
        'followup_date',
        'status',
        'notes',
        'reminder',
    ];

    # Modify methods Model
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
