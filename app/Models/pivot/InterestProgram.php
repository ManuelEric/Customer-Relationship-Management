<?php

namespace App\Models\pivot;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InterestProgram extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_interest_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id', 
        'status',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }
}
