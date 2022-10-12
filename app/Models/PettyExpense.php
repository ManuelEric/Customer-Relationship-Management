<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyExpense extends Model
{
    use HasFactory;

    protected $table = 'tbl_pettyexpense';
    protected $primaryKey = 'pettyexpenses_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'pettyexpenses_inv', 
        'pettyexpenses_date', 
        'pettyexpenses_supplier', 
        'pettyexpenses_type', 
        'pettyexpenses_paymentfrom', 
        'pettyexpenses_total',
    ];
}
