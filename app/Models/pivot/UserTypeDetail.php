<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserTypeDetail extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_user_type_detail';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'user_type_id',
        'user_id', 
        'start_date',
        'end_date',
        'status'
    ];
}
