<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class InvoiceSchool extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_invsch';
    protected $primaryKey = 'invsch_num';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'invsch_num',
        'invsch_id',
        'schprog_id',
        'invsch_price',
        'invsch_participants',
        'invsch_disc',
        'invsch_totprice',
        'invsch_words',
        'invsch_date',
        'invsch_duedate',
        'invsch_pm',
        'invsch_notes',
        'invsch_tnc',
        'invsch_status',
    ];
}
