<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_parents';
    protected $primaryKey = 'pr_id';
    

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'pr_id',
        'pr_firstname',
        'pr_lastname',
        'pr_mail',
        'pr_phone',
        'pr_dob',
        'pr_insta',
        'pr_state',
        'pr_address',
        'pr_password',
    ];

    public function student()
    {
        return $this->hasMany(Student::class, 'pr_id', 'pr_id');
    }
}
