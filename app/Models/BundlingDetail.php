<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BundlingDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_bundling_dtl';
    protected $primaryKey = 'id';
    
    public $incrementing = true;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'bundling_id',
        'clientprog_id',
    ];


}
