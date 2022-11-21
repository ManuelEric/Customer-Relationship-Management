<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversityPic extends Model
{
    use HasFactory;

    protected $table = 'tbl_univ_pic';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'univ_id',
        'name', 
        'title', 
        'phone',
        'email',
    ];

    public function university()
    {
        return $this->belongsTo(University::class, 'univ_id', 'univ_id');
    }
}
