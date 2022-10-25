<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'asset_running_stock',
        'asset_unit', 
        'asset_condition', 
        'asset_notes', 
        'asset_status',
    ];

    public function assetNotes(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strip_tags($value),
        );
    }

    public static function whereAssetId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;
        
        $instance = new static;

        return $instance->newQuery()->find($id, 'asset_id');
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'tbl_asset_used', 'asset_id', 'user_id')->withPivot(
            [
                'start_used',
                'amount_used',
                'end_used',
                'condition',
                'status'
            ]
        );
    }
}
