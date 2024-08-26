<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_lead';
    protected $primaryKey = 'lead_id';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'lead_id',
        'lead_name',
    ];

    public function student()
    {
        return $this->hasMany(Student::class, 'lead_id', 'lead_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'lead_id', 'lead_id');
    }
}
