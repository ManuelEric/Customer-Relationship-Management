<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Asset extends Model
{
    use HasFactory;

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

    public static function whereAssetId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;
        
        $instance = new static;

        return $instance->newQuery()->find($id, 'asset_id');
    }
}
