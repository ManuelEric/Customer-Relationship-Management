<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCountry extends Model
{
    use HasFactory;

    protected $table = 'tbl_country';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'tag'
    ];

    public function tagCountry()
    {
        return $this->belongsTo(Tag::class, 'tag', 'id');
    }
   
}
