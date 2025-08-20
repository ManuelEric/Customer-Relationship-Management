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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal whereContributionInPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal whereContributionToTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal whereDivisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal whereHotLeadsTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal whereInitialConsultTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal whereLeadNeeded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal whereMonthlyTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewTargetSignal whereRevenueTarget($value)
 *
 * @mixin \Eloquent
 */
class ViewTargetSignal extends Model
{
    use HasFactory;

    protected $table = 'target_signal_view';
}
