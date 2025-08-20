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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket whereBucketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket whereInitialprogramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket whereParamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket whereWeightExistingMentee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket whereWeightExistingNonMentee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramBucket whereWeightNew($value)
 *
 * @mixin \Eloquent
 */
class ProgramBucket extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_program_buckets_params';

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
