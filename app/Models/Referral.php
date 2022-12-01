<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $table = 'tbl_referral';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'partner_id',
        'prog_id',
        'empl_id',
        'referral_type',
        'additional_prog_name',
        'currency',
        'number_of_student',
        'revenue',
        'ref_date',
        'notes',
    ];
}
