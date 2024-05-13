<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Bundling extends Model
{
    use HasFactory;

    protected $table = 'tbl_bundling';
    protected $primaryKey = 'uuid';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
    ];


}
