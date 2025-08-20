<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $bucket_id
 * @property int $initialprogram_id
 * @property int $param_id
 * @property int|null $weight_existing_non_mentee
 * @property int|null $weight_existing_mentee
 * @property int|null $weight_new
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket whereBucketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket whereInitialprogramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket whereParamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket whereWeightExistingMentee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket whereWeightExistingNonMentee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LeadBucket whereWeightNew($value)
 *
 * @mixin \Eloquent
 */
class LeadBucket extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_lead_bucket_params';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'bucket_id',
        'initialprogram_id',
        'param_id',
        'weight_existing_non_mentee',
        'weight_existing_mentee',
        'weight_new',
    ];
}
