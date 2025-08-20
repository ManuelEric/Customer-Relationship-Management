<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $parent_name
 * @property string|null $parent_phone
 * @property string|null $parent_email
 * @property string|null $child_name
 * @property string|null $child_graduation_year
 * @property string|null $child_school
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead whereChildGraduationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead whereChildName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead whereChildSchool($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead whereParentEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead whereParentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead whereParentPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedMetaLead whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class FailedMetaLead extends Model
{
    protected $fillable = [
        'parent_name',
        'parent_phone',
        'parent_email',
        'child_name',
        'child_graduation_year',
        'child_school',
    ];
}
