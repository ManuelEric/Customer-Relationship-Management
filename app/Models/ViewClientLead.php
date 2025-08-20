<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $id
 * @property string|null $graduation_year
 * @property string $name
 * @property string|null $phone
 * @property int|null $real_grade
 * @property int|null $grade_client_lead
 * @property int|null $grade
 * @property string|null $school
 * @property string|null $type_school
 * @property string $lead_source
 * @property int|null $is_funding 0: False, 1: True
 * @property string|null $interested_country
 * @property string|null $major
 * @property int|null $school_categorization
 * @property int|null $grade_categorization
 * @property int|null $country_categorization
 * @property int|null $major_categorization
 * @property string|null $roles
 * @property string|null $type
 * @property string|null $register_as
 * @property int|null $active status aktif client
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Program> $interestPrograms
 * @property-read int|null $interest_programs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InitialProgram> $leadStatus
 * @property-read int|null $lead_status_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereCountryCategorization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereGradeCategorization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereGradeClientLead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereGraduationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereInterestedCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereIsFunding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereLeadSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereMajor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereMajorCategorization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereRealGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereRegisterAs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereRoles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereSchool($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereSchoolCategorization($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientLead whereTypeSchool($value)
 *
 * @mixin \Eloquent
 */
class ViewClientLead extends Model
{
    use HasFactory;

    protected $table = 'client_lead';

    protected $keyType = 'string';

    public function interestPrograms()
    {
        return $this->belongsToMany(Program::class, 'tbl_interest_prog', 'client_id', 'prog_id')->withTimestamps();
    }

    public function leadStatus()
    {
        return $this->belongsToMany(InitialProgram::class, 'tbl_client_lead_tracking', 'client_id', 'initialprogram_id')->withPivot(['id', 'group_id', 'client_id', 'initialprogram_id', 'type', 'total_result', 'status', 'reason_id'])->withTimestamps();
    }
}
