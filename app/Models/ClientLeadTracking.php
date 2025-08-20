<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property-read \App\Models\UserClient $client
 * @property-read \App\Models\InitialProgram $initProg
 * @property-read mixed $lead_status
 * @property-read mixed $program_status
 * @property-read \App\Models\Reason|null $reason
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
    use HasFactory;

    protected $table = 'tbl_client_lead_tracking';

    protected $primaryKey = 'id';

    protected $appends = ['lead_status', 'program_status'];

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'group_id',
        'client_id',
        'initialprogram_id',
        'type',
        'total_result',
        'potential_point', // for digital team only
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

    public function programStatus(): Attribute
    {
        if ($this->type == 'Program') {

            if ($this->total_result >= 0.5) {
                return Attribute::make(
                    get: fn ($value) => 'Yes',
                );
            } else {
                return Attribute::make(
                    get: fn ($value) => 'No',
                );
            }

        } else {
            return Attribute::make(
                get: fn ($value) => null,
            );
        }
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function client()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }

    public function initProg()
    {
        return $this->belongsTo(InitialProgram::class, 'initialprogram_id', 'id');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class, 'reason_id', 'reason_id');
    }
}
