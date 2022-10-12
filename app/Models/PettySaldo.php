<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettySaldo extends Model
{
    use HasFactory;

    protected $table = 'tbl_pettysaldo';
    protected $primaryKey = 'pettysaldo_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'pettysaldo_month', 
        'pettysaldo_year', 
        'pettysaldo_inflow', 
        'pettysaldo_expenses', 
        'pettysaldo_balance',
    ];
}
