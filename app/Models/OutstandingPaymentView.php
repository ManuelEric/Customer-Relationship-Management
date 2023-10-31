<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutstandingPaymentView extends Model
{
    use HasFactory;

    protected $table = 'outstanding_payment_view';
}
