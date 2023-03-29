<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corp extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_corp';
    protected $primaryKey = 'corp_id';
    
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'corp_id',
        'corp_name',
        'corp_industry',
        'corp_mail',
        'corp_phone',
        'corp_insta',
        'corp_site',
        'corp_region',
        'corp_address',
        'corp_note',
        'corp_password',
        'crop_datecreated',
        'corp_datelastedit',
    ];

    public function detail()
    {
        return $this->hasMany(CorpDetail::class, 'corp_id', 'corp_id');
    }
}
