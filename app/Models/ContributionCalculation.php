<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributionCalculation extends Model
{
    use HasFactory;

    protected $table = 'contribution_calculation_tmp';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'divisi',
        'contribution_in_percent',
        'contribution_to_target',
        'initial_consult_target',
        'hot_leads_target',
        'leads_needed'
    ];
}
