<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $divisi
 * @property float $contribution_in_percent
 * @property int|null $monthly_target
 * @property int|null $contribution_to_target
 * @property int|null $initial_consult_target
 * @property int|null $hot_leads_target
 * @property int|null $lead_needed
 * @property int|null $revenue_target
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal whereContributionInPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal whereContributionToTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal whereDivisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal whereHotLeadsTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal whereInitialConsultTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal whereLeadNeeded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal whereMonthlyTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TargetSignal whereRevenueTarget($value)
 *
 * @mixin \Eloquent
 */
class TargetSignal extends Model
{
    use HasFactory;

    protected $table = 'target_signal_view';
}
