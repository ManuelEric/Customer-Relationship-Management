<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Receipt extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_receipt';
    protected $primaryKey = 'receipt_num';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'receipt_num',
        'receipt_id',
        'receipt_cat',
        'inv_id',
        'invdtl_id',
        'pt_id',
        'receipt_mtd',
        'receipt_cheque',
        'receipt_amount',
        'receipt_amountusd',
        'receipt_words',
        'receipt_wordsusd',
        'receipt_date',
        'receipt_notes',
        'receipt_status',
        'receipt_refund',
    ];
}
