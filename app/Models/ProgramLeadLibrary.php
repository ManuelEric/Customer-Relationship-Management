<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramLeadLibrary extends Model
{
    use HasFactory;

    protected $table = 'tbl_program_lead_library';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'programbucket_id',
        'leadbucket_id',
        'value_category',
        'new',
        'existing_mentee',
        'existing_non_mentee'
    ];
}
