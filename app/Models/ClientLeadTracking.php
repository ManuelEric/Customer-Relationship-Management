<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientLeadTracking extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_lead_tracking';
    protected $primaryKey = 'id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'initialprogram_id',
        'type',
        'total_result',
        'status',
    ];
}
