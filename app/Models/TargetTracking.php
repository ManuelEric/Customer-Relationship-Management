<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $divisi
 * @property int $target_lead
 * @property int $achieved_lead
 * @property int $target_hotleads
 * @property int $achieved_hotleads
 * @property int $target_initconsult
 * @property int $achieved_initconsult
 * @property int $contribution_target
 * @property int $contribution_achieved
 * @property int $revenue_achieved
 * @property int $revenue_target
 * @property string $month_year
 * @property int $added the number of deviation from month before
 * @property int $status 0: incomplete, 1: complete
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereAchievedHotleads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereAchievedInitconsult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereAchievedLead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereAdded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereContributionAchieved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereContributionTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereDivisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereMonthYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereRevenueAchieved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereRevenueTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereTargetHotleads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereTargetInitconsult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereTargetLead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetTracking whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TargetTracking extends Model
{
    use HasFactory;

    protected $table = 'target_tracking';

    // Modify methods Model
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
