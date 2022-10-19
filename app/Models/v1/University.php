<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_univ';
    protected $primaryKey = 'univ_id';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'univ_id',
        'univ_name',
        'univ_address',
        'univ_country',
    ];

    # relation
    public function mentor()
    {
        return $this->hasMany(Mentor::class, 'univ_id', 'univ_id');
    }

    public function editor()
    {
        return $this->hasMany(Editor::class, 'univ_id', 'univ_id');
    }
}
