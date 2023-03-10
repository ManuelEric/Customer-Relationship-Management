<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


class PartnerProg extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_corprog';
    protected $primaryKey = 'corprog_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'corprog_id',
        'corp_id',
        'prog_id',
        'corprog_type',
        'corprog_datefirstdiscuss',
        'corprog_datelastdiscuss',
        'corprog_notes',
        'corprog_status',
    ];
}
