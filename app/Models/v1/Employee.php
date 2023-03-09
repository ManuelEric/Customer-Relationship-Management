<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_empl';
    protected $primaryKey = 'empl_id';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'empl_id',
        'empl_firstname',
        'empl_lastname',
        'empl_email',
        'empl_address',
        'empl_phone',
        'empl_emergency_contact',
        'empl_datebirth',
        'empl_graduatefr',
        'empl_graduatefr_magister',
        'empl_major',
        'empl_major_register',
        'empl_department',
        'empl_hiredate',
        'empl_status',
        'empl_statusenddate',
        'empl_isactive',
        'empl_cv',
        'empl_bankaccountname',
        'empl_bankaccount',
        'empl_nik',
        'empl_idcard',
        'empl_npwp',
        'empl_tax',
        'empl_healthinsurance',
        'empl_emplinsurance',
        'empl_password',
        'empl_role',
        'empl_export',
        'empl_lastupdatedate'
    ];

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'empl_id', 'empl_id');
    }
}
