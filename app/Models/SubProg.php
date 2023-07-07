<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubProg extends Model
{
    use HasFactory;

    protected $table = 'tbl_sub_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'sub_prog_name',
        'sub_prog_status',
    ];

    # relation

    public function mainProgram()
    {
        return $this->belongsTo(MainProg::class, 'main_prog_id', 'id');
    }

    public function program()
    {
        return $this->hasMany(SubProg::class, 'sub_prog_id', 'id');
    }

    public function spesificConcern()
    {
        return $this->belongsToMany(InitialProgram::class, 'tbl_initial_prog_sub_lead', 'subprogram_id', 'id');
    }
}
