<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Vendor extends Model
{
    use HasFactory;

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
        'vendor_notes'
    ];

    public static function whereVendorId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;
        
        $instance = new static;

        return $instance->newQuery()->where('vendor_id', $id)->first();
    }
}
