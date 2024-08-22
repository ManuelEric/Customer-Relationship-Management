<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TargetTracking extends Model
{
    use HasFactory;

    protected $table = 'target_tracking';

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Delete cache Count Alarm, notification,
        Cache::has('countAlarm') ? Cache::forget('countAlarm') : null;
        Cache::has('notification') ? Cache::forget('notification') : null;

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Delete cache Count Alarm, notification,
        Cache::has('countAlarm') ? Cache::forget('countAlarm') : null;
        Cache::has('notification') ? Cache::forget('notification') : null;

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model
        // Delete cache Count Alarm, notification,
        Cache::has('countAlarm') ? Cache::forget('countAlarm') : null;
        Cache::has('notification') ? Cache::forget('notification') : null;

        return $model;
    }

}
