<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_asset';
    protected $primaryKey = 'asset_id';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'asset_id',
        'asset_name',
        'asset_merktype',
        'asset_dateachieved',
        'asset_amount',
        'asset_unit',
        'asset_condition',
        'asset_notes',
        'asset_status',
    ];
}
