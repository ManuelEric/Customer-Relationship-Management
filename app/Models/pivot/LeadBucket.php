<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LeadBucket extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_lead_bucket_params';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'bucket_id',
        'initialprogram_id',
        'param_id',
        'weight_existing_non_mentee',
        'weight_existing_mentee',
        'weight_new'
    ];
}
