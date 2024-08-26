<?php

namespace App\Models\v1;

use App\Models\Referral as ReferralV2;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_pt';
    protected $primaryKey = 'pt_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'pt_id',
        'pt_name',
        'pt_email',
        'pt_phone',
        'pt_ins',
        'pt_address',
    ];

    public function ref_prog()
    {
        return $this->hasMany(ReferralV2::class, 'partner_id', 'corp_id');
    }

    public function receipt()
    {
        return $this->hasMany(Receipt::class, 'pt_id', 'pt_id');
    }
}
