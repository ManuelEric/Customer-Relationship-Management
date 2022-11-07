<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainProg extends Model
{
    use HasFactory;

    protected $table = 'tbl_main_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'prog_name',
        'prog_status',
    ];

    # relation
    public function subProgram()
    {
        return $this->hasMany(SubProg::class, 'main_prog_id', 'id');
    }
}
