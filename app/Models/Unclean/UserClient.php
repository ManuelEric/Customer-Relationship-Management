<?php

namespace App\Models\Unclean;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClient extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_client';
    protected $primaryKey = 'id';
    

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'st_id',
        'uuid',
        'first_name',
        'last_name',
        'mail',
        'phone',
        'phone_desc',
        'dob',
        'insta',
        'state',
        'city',
        'postal_code',
        'address',
        'sch_id',
        // 'sch_uuid',
        'st_grade',
        'lead_id',
        'eduf_id',
        'partner_id',
        'event_id',
        'st_levelinterest',
        'graduation_year',
        'gap_year',
        'st_abryear',
        // 'st_abrcountry',
        'st_statusact',
        'st_note',
        'st_statuscli',
        // 'st_prospect_status',
        'st_password',
        'preferred_program',
        'is_funding',
        'scholarship',
        'is_verified',
        'register_as',
        'referral_code',
        'category',
        'took_ia',
        'created_at',
        'updated_at',
    ];

   
}
