<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_vendor';
    protected $primaryKey = 'vendor_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'vendor_id',
        'vendor_name',
        'vendor_address',
        'vendor_phone',
        'vendor_type',
        'vendor_material',
        'vendor_size',
        'vendor_unitprice',
        'vendor_processingtime',
        'vendor_notes',
        'vendor_lastupdatedate',
    ];
}
