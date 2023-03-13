<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorpDetail extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_corpdetail';
    protected $primaryKey = 'corpdetail_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'corp_id',
        'corpdetail_fullname',
        'corpdetail_mail',
        'corpdetail_linkedin',
        'corpdetail_phone',
    ];

    public function corp()
    {
        return $this->belongsTo(Corp::class, 'corp_id', 'corp_id');
    }
}
