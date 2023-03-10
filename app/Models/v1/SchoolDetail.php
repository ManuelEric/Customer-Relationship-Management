<?php

namespace App\Models\v1;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDetail extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_schdetail';
    protected $primaryKey = 'schdetail_id';
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'schdetail_fullname',
        'schdetail_email',
        'schdetail_grade',
        'schdetail_position',
        'schdetail_phone',
    ];
}
