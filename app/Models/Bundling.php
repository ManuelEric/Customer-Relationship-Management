<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Bundling extends Model
{
    use HasFactory;

    protected $table = 'tbl_bundling';
    protected $primaryKey = 'uuid';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
    ];

    /**
     * 
     * Get the invoice b2c for the bundling
     * 
     */
    public function invoice_b2c(): HasOne
    {
        return $this->hasOne(InvoiceProgram::class, 'bundling_id', 'uuid');
    }

    public function details(): HasMany
    {
        return $this->hasMany(BundlingDetail::class, 'bundling_id', 'uuid');
    }

}
