<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyInflow extends Model
{
    use HasFactory;

    protected $table = 'tbl_pettyinflow';
    protected $primaryKey = 'pettyinflow_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'pettinflow_date', 
        'pettyinflow_total',
    ];
}
