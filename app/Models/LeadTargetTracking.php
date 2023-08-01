<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadTargetTracking extends Model
{
    use HasFactory;

    protected $table = 'target_tracking';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'divisi',
        'target',
        'achieved',
        'added',
        'month',
        'year',
        'status'
    ];
}
