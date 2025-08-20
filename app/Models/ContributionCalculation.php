<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $divisi
 * @property float $contribution_in_percent
 * @property int|null $contribution_to_target
 * @property int|null $initial_consult_target # of IC (1,5x target)
 * @property int|null $hot_leads_target # of hot leads (2x IC)
 * @property int|null $leads_needed
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation whereContributionInPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation whereContributionToTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation whereDivisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation whereHotLeadsTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation whereInitialConsultTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionCalculation whereLeadsNeeded($value)
 *
 * @mixin \Eloquent
 */
class ContributionCalculation extends Model
{
    use HasFactory;

    protected $table = 'contribution_calculation_tmp';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'divisi',
        'contribution_in_percent',
        'contribution_to_target',
        'initial_consult_target',
        'hot_leads_target',
        'leads_needed',
    ];
}
