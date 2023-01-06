<?php

namespace App\Models;

use App\Models\Invb2b;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class InvDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_invdtl';
    protected $primaryKey = 'invdtl_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'invdtl_id',
        'invb2b_id',
        'inv_id',
        'invdtl_installment',
        'invdtl_duedate',
        'invdtl_percentage',
        'invdtl_amount',
        'invdtl_amountidr',
        'invdtl_status',
        'invdtl_cursrate',
        'invdtl_currency'
    ];

    public function inv_b2b()
    {
        return $this->belongsTo(Invb2b::class, 'invb2b_id', 'invb2b_id');
    }

}
