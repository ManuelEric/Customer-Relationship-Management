<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSubject extends Pivot
{
    protected $table = 'tbl_user_subjects';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id', 
        'subject_id', 
        'feehours',
        'feesession',
    ];

}
