<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


class SchoolProgramFix extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_schprogfix';
    protected $primaryKey = 'schprogfix_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'schprogfix_id',
        'schprog_id',
        'schprogfix_eventstartdate',
        'schprogfix_eventenddate',
        'schprogfix_eventplace',
        'schprogfix_participantsnum',
        'schprogfix_status',
        'schprogfix_totalhours',
        'schprogfix_notes',
    ];
}
