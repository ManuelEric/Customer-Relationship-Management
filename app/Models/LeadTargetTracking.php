<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadTargetTracking extends Model
{
    use HasFactory;

    protected $table = 'target_tracking';

    public $timestamps = true;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'divisi',
        'target_lead',
        'achieved_lead',
        'target_hotleads',
        'achieved_hotleads',
        'target_initconsult',
        'achieved_initconsult',
        'contribution_target',
        'contribution_achieved',
        'status',
        'month_year'
    ];
}
