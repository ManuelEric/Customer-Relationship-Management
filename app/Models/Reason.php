<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;

    protected $table = 'tbl_reason';
    protected $primaryKey = 'reason_id';


    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'reason_id',
        'reason_name',
        'created_at',
        'updated_at',
    ];

    public function school_program()
    {
        return $this->hasMany(SchoolProgram::class, 'reason_id', 'reason_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'reason_id', 'reason_id');
    }
}
