<?php

namespace App\Models\pivot;

use App\Models\PhaseDetail;
use App\Models\Stream;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_role_id
 * @property int $stream_id
 * @property int|null $engagement_type_id
 * @property string|null $package
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $agreement
 * @property int|null $head
 * @property int|null $additional_fee
 * @property string|null $grade
 * @property int|null $fee_individual
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read PhaseDetail|null $engagement_type
 * @property-read Stream $stream
 * @property-read \App\Models\pivot\UserRole $user_roles
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereAdditionalFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereAgreement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereEngagementTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereFeeIndividual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream wherePackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereStreamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserStream whereUserRoleId($value)
 *
 * @mixin \Eloquent
 */
class UserStream extends Model
{
    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'user_role_id',
        'stream_id',
        'engagement_type_id',
        'package',
        'fee_individual',
        'grade',
        'additional_fee',
        'agreement',
        'head',
        'start_date',
        'end_date',
    ];

    public function stream()
    {
        return $this->belongsTo(Stream::class, 'stream_id', 'id');
    }

    public function user_roles()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id', 'id')->with('role');
    }

    public function engagement_type()
    {
        return $this->belongsTo(PhaseDetail::class, 'engagement_type_id', 'id');
    }
}
