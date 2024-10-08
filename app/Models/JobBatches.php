<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class JobBatches extends Model
{
    use HasFactory;

    protected $table = 'job_batches';
    
    public $incrementing = false;
    public $timestamps = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name', 
        'total_jobs', 
        'pending_jobs', 
        'failed_jobs', 
        'failed_job_ids',
        'options', 
        'cancelled_at', 
        'created_at', 
        'finished_at',
        'total_imported',
        'total_data',
        'log_details',
        'type',
        'category'
    ];

    
}
