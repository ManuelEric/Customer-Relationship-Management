<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientProgramDetail extends Pivot
{
    use HasFactory;

    protected $table = 'client_program_details';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'slot',
        'grade', 
        'nation',
    ];
}
