<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Volunteer extends Model
{
    use HasFactory;

    protected $table = 'tbl_volunt';
    protected $primaryKey = 'volunt_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
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
        'position_id'
    ];

    public static function whereVolunteerId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->find($id, 'volunt_id');
    }
}
