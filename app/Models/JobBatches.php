<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property int $total_jobs
 * @property int $pending_jobs
 * @property int $failed_jobs
 * @property string $failed_job_ids
 * @property string|null $options
 * @property int|null $cancelled_at
 * @property int $created_at
 * @property int|null $finished_at
 * @property string|null $log_details
 * @property int $total_imported
 * @property int $total_data
 * @property string|null $type
 * @property string|null $category
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereFailedJobIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereFailedJobs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereLogDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches wherePendingJobs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereTotalData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereTotalImported($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereTotalJobs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobBatches whereType($value)
 *
 * @mixin \Eloquent
 */
class JobBatches extends Model
{
    use HasFactory;

    protected $table = 'job_batches';

    public $incrementing = false;

    public $timestamps = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'total_jobs',
        'pending_jobs',
        'failed_jobs',
        'failed_job_ids',
        'options',
        'cancelled_at',
        'created_at',
        'finished_at',
        'total_imported',
        'total_data',
        'log_details',
        'type',
        'category',
    ];
}
