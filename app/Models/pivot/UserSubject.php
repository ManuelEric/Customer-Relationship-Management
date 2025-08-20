<?php

namespace App\Models\pivot;

use App\Models\Pivot\UserRole;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $user_role_id
 * @property int|null $subject_id
 * @property string|null $start_date Active Agreement Periode
 * @property string|null $end_date Active Agreement Periode
 * @property string|null $curriculum
 * @property string|null $year
 * @property string|null $agreement
 * @property int|null $head
 * @property int|null $additional_fee
 * @property string|null $grade
 * @property int|null $fee_individual
 * @property int|null $fee_group
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Subject|null $subject
 * @property-read \App\Models\pivot\UserRole $user_roles
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereAdditionalFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereAgreement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereCurriculum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereFeeGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereFeeIndividual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereUserRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSubject whereYear($value)
 *
 * @mixin \Eloquent
 */
class UserSubject extends Pivot
{
    protected $table = 'tbl_user_subjects';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'user_role_id',
        'subject_id',
        'curriculum',
        'fee_individual',
        'fee_group',
        'grade',
        'additional_fee',
        'agreement',
        'head',
        'start_date',
        'end_date',
        'year',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function user_roles()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id', 'id')->with('role');
    }
}
