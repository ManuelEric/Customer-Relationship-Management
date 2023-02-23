<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Axis extends Model
{
    use HasFactory;

    protected $table = 'tbl_axis';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'top',
        'left',
        'scaleX',
        'scaleY',
        'angle',
        'flipX',
        'flipY',
    ];
}
