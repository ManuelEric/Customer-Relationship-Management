<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $volunt_id
 * @property string|null $volunt_firstname
 * @property string|null $volunt_lastname
 * @property string|null $volunt_address
 * @property string|null $volunt_mail
 * @property string|null $volunt_phone
 * @property string|null $volunt_idcard
 * @property string|null $volunt_npwp
 * @property string|null $empl_insurance
 * @property string|null $health_insurance
 * @property int|null $volunt_npwp_number
 * @property int $volunt_nik
 * @property string|null $volunt_bank_name
 * @property int $volunt_bank_accnumber
 * @property string $volunt_bank_accname
 * @property string $volunt_cv
 * @property int|null $volunt_status
 * @property string|null $volunt_lasteditdate
 * @property int|null $position_id
 * @property int|null $major_id
 * @property string|null $univ_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereEmplInsurance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereHealthInsurance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereMajorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereUnivId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntBankAccname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntBankAccnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntCv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntIdcard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntLasteditdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntNpwp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntNpwpNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Volunteer whereVoluntStatus($value)
 *
 * @mixin \Eloquent
 */
class Volunteer extends Model
{
    use HasFactory;

    protected $table = 'tbl_volunt';

    protected $primaryKey = 'volunt_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'volunt_id',
        'volunt_firstname',
        'volunt_lastname',
        'volunt_address',
        'volunt_mail',
        'volunt_phone',
        'volunt_graduatedfr',
        'volunt_major',
        'volunt_position',
        'volunt_idcard',
        'volunt_npwp',
        'volunt_cv',
        'volunt_bank_name',
        'volunt_bank_accname',
        'volunt_bank_accnumber',
        'volunt_nik',
        'volunt_idcard',
        'volunt_npwp_number',
        'volunt_npwp',
        'health_insurance',
        'empl_insurance',
        'volunt_status',
        'univ_id',
        'major_id',
        'position_id',
    ];

    public static function whereVolunteerId($id)
    {
        if (is_array($id) && empty($id)) {
            return collect();
        }

        $query = static::query();

        if (is_array($id)) {
            return $query->whereIn('volunt_id', $id)->get();
        }

        return $query->where('volunt_id', $id)->first();
    }
}
