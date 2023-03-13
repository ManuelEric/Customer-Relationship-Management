<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eduf extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_eduf';
    protected $primaryKey = 'eduf_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'eduf_id',
        'eduf_organizer',
        'eduf_place',
        'eduf_picname',
        'eduf_picmail',
        'eduf_picphone',
        'eduf_firstdisdate',
        'eduf_lastdisdate',
        'eduf_eventstartdate',
        'eduf_eventenddate',
        'eduf_status',
        'eduf_picallin',
        'eduf_notes',
    ];
}
