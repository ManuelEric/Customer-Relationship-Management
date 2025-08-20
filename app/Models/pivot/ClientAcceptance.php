<?php

namespace App\Models\pivot;

use App\Models\Major;
use App\Models\MajorGroup;
use App\Models\University;
use App\Models\UserClient;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $client_id
 * @property string $univ_id
 * @property int|null $major_group_id
 * @property string|null $major_name
 * @property int|null $major_id
 * @property string|null $category
 * @property string $status
 * @property int $is_picked Final Decision
 * @property string|null $requirement_link
 * @property string|null $early_action
 * @property string|null $early_decision
 * @property string|null $regular_deadline
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read UserClient $client
 * @property-read mixed $get_major_name
 * @property-read Major|null $major
 * @property-read MajorGroup|null $major_group
 * @property-read University $university
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereEarlyAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereEarlyDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereIsPicked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereMajorGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereMajorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereMajorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereRegularDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereRequirementLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereUnivId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ClientAcceptance extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_client_acceptance';

    protected $fillable = [
        'client_id',
        'univ_id',
        'major_group_id',
        'major_name',
        'major_id', // ! unused since there were a major_group_id
        'category',
        'status',
        'is_picked',
        'requirement_link',
        'early_action',
        'early_decision',
        'regular_deadline',
    ];

    public function client()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'univ_id', 'univ_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id', 'id');
    }

    public function major_group()
    {
        return $this->belongsTo(MajorGroup::class, 'major_group_id', 'id');
    }

    public function getMajorName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value) {
                if ($this->major_name == null && $this->major_id == null) {
                    $major = null;
                } else {
                    $major = $this->major_name ?? $this->major->name;
                }

                return $major;
            }
        );
    }
}
