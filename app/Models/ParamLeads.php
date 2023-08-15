<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParamLeads extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_param_lead';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
    ];

    public function programBucketParams()
    {
        return $this->belongsToMany(InitialProgram::class, 'tbl_program_buckets_params', 'param_id', 'initialprogram_id')->using(ProgramBucket::class)->withTimestamps();
    }
}
