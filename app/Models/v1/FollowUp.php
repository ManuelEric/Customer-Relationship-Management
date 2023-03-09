<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_followup';
    protected $primaryKey = 'flw_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'flw_id',
        'stprog_id',
        'flw_date',
        'flw_mark',
        'flw_notes',
        'flw_sent',
    ];

    public function clientProgram()
    {
        return $this->belongsTo(ClientProgram::class, 'stprog_id', 'stprog_id');
    }
}
