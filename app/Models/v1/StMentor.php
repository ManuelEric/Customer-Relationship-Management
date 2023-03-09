<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StMentor extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_stmentor';
    protected $primaryKey = 'stmentor_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'stmentor_id',
        'stprog_id',
        'mt_id1',
        'mt_id2'
    ];

    public function mentorOfClientProgram()
    {
        return $this->belongsTo(ClientProgram::class, 'stprog_id', 'stprog_id');
    }
    
}
