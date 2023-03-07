<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_prog';
    protected $primaryKey = 'prog_id';
    
    public $incrementing = false;
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'prog_id',
        'prog_main',
        'main_nummber',
        'prog_sub',
        'prog_program',
        'prog_type',
        'prog_mentor',
        'prog_payment',
    ];
}
