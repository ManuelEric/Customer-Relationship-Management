<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoringParam extends Model
{
    use HasFactory;

    protected $table = 'tbl_scoring_param';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'category',
        'max_score',
    ];
}
