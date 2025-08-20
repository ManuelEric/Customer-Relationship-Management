<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_role_id
 * @property string $category
 * @property string $start_date
 * @property string $end_date
 * @property string $agreement
 * @property int $fee_individual
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\pivot\UserRole|null $user_roles
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement whereAgreement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement whereFeeIndividual($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EditorAgreement whereUserRoleId($value)
 *
 * @mixin \Eloquent
 */
class EditorAgreement extends Model
{
    protected $fillable = [
        'id',
        'user_role_id',
        'category',
        'fee_individual',
        'agreement',
        'start_date',
        'end_date',
    ];

    public function user_roles()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id', 'id')->with('role');
    }
}
