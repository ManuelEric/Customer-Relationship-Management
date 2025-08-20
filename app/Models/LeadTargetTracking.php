<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereAchievedHotleads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereAchievedInitconsult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereAchievedLead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereAdded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereContributionAchieved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereContributionTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereDivisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereMonthYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereRevenueAchieved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereRevenueTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereTargetHotleads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereTargetInitconsult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereTargetLead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadTargetTracking whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LeadTargetTracking extends Model
{
    use HasFactory;

    protected $table = 'target_tracking';

    public $timestamps = true;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'divisi',
        'target_lead',
        'achieved_lead',
        'target_hotleads',
        'achieved_hotleads',
        'target_initconsult',
        'achieved_initconsult',
        'contribution_target',
        'contribution_achieved',
        'status',
        'month_year',
    ];
}
