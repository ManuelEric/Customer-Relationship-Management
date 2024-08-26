<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_inv';
    protected $primaryKey = 'inv_num';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'inv_num',
        'inv_id',
        'stprog_id',
        'inv_category',
        'inv_priceus',
        'inv_priceidr',
        'inv_earlybirdusd',
        'inv_earlybirdidr',
        'inv_discusd',
        'inv_discidr',
        'inv_session',
        'inv_duration',
        'inv_totprusd',
        'inv_totpidr',
        'inv_words',
        'inv_wordsusd',
        'inv_paymentmethod',
        'inv_date',
        'inv_duedate',
        'inv_notes',
        'inv_tnc',
        'inv_status',
        'reminder_status',
        'reminder_notes'
    ];

    public function clientProgram()
    {
        return $this->belongsTo(ClientProgram::class, 'stprog_id', 'stprog_id');
    }

    public function installment()
    {
        return $this->hasMany(InvoiceDtl::class, 'inv_id', 'inv_id');
    }

    public function receipt()
    {
        return $this->hasMany(Receipt::class, 'inv_id', 'inv_id');
    }
}
