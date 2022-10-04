<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_mt';
    protected $primaryKey = 'mt_id';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'mt_id',
        'mt_firstn',
        'mt_lastn',
        'mt_address',
        'mt_major',
        'univ_id',
        'mt_email',
        'mt_phone',
        'mt_password',
        'mt_cv',
        'mt_ktp',
        'mt_banknm',
        'mt_bankacc',
        'mt_npwp',
        'mt_status',
        'mt_istutor',
        'mt_tsubject',
        'mt_feehours',
        'mt_feesession',
        'mt_lastcontactdate',
        'mt_notes',
        'mt_lastupdate',
    ];
}
