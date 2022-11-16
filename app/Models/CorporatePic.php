<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporatePic extends Model
{
    use HasFactory;

    protected $table = 'tbl_corp_pic';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'corp_id',
        'pic_name',
        'pic_mail',
        'pic_linkedin',
        'pic_phone',
    ];

    public function corporate()
    {
        return $this->belongsTo(Corporate::class, 'corp_id', 'corp_id');
    }
}
