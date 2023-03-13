<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDtl extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_invdtl';
    protected $primaryKey = 'invdtl_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'inv_id',
        'invdtl_statusname',
        'invdtl_duedate',
        'invdtl_percentage',
        'invdtl_amountusd',
        'invdtl_amountidr',
        'invdtl_status',
        'reminder_status',
        'reminder_notes',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'inv_id', 'inv_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'invdtl_id', 'invdtl_id');
    }
}
