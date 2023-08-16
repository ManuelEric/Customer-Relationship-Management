<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProgramBucket extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_program_buckets_params';

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
