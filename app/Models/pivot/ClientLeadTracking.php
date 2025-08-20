<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $group_id
 * @property string $client_id
 * @property int $initialprogram_id
 * @property string $type
 * @property float $total_result
 * @property float $potential_point this point is for digital team tracker
 * @property int $status
 * @property int|null $reason_id
 * @property string|null $reason_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $lead_status
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereInitialprogramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking wherePotentialPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereReasonNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereTotalResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientLeadTracking whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ClientLeadTracking extends Pivot
{
    protected $table = 'tbl_client_lead_tracking';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'group_id',
        'initialprogram_id',
        'type',
        'total_result',
        'status',
        'reason_id',
    ];

    public function leadStatus(): Attribute
    {
        if ($this->type == 'Lead') {

            if ($this->total_result >= 0.65) {
                return Attribute::make(
                    get: fn ($value) => 'Hot',
                );
            } elseif ($this->total_result >= 0.35 && $this->total_result < 0.65) {
                return Attribute::make(
                    get: fn ($value) => 'Warm',
                );

            } elseif ($this->total_result < 0.35) {
                return Attribute::make(
                    get: fn ($value) => 'Cold',
                );
            }

        } else {
            return Attribute::make(
                get: fn ($value) => null,
            );
        }
    }
}
