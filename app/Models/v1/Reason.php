<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_reason';
    protected $primaryKey = 'reason_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'reason_id',
        'reason_name',
    ];

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'reason_id', 'reason_id');
    }
}
