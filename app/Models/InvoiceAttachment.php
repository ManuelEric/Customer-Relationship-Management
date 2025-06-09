<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;


class InvoiceAttachment extends Model
{
    use HasFactory;

    protected $table = 'tbl_inv_attachment';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'inv_id',
        'invb2b_id',
        'currency',
        'sign_status',
        'recipient',
        'approve_date',
        'send_to_client',
        'attachment',
    ];

    public function scopeSelectAttachment(Builder $query, $invoice_type, $identifier, $currency)
    {
        $query->
            when($invoice_type == "B2B", function ($query) use ($identifier, $currency) { // for invoice type: B2B
                $query->where('invb2b_id', $identifier)->where('currency', $currency);
            }, function ($query) use ($identifier, $currency) { // for invoice type: Program as default
                $query->where('inv_id', $identifier)->where('currency', $currency);
            });
    }

    public function invoiceProgram()
    {
        return $this->belongsTo(InvoiceProgram::class, 'inv_id', 'inv_id');
    }

    public function invoiceB2b()
    {
        return $this->belongsTo(Invb2b::class, 'invb2b_id', 'invb2b_id');
    }
}
