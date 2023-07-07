<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_initial_program_lead';
    protected $primaryKey = 'id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];


    public function sub_prog()
    {
        return $this->belongsTo(SubProg::class, 'sub_id', 'id');
    }
}
