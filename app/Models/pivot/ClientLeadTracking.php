<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientLeadTracking extends Pivot
{

    protected $table = 'tbl_client_lead_tracking';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'initialprogram_id',
        'type',
        'total_result',
        'status'
    ];
}
