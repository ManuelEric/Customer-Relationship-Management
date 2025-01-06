<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


class PartnerAgreement extends Model
{
    use HasFactory;

    protected $table = 'tbl_partner_agreement';
    protected $primaryKey = 'id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'corp_id',
        'agreement_name',
        'agreement_type',
        'attachment',
        'start_date',
        'end_date',
        'corp_pic',
        'empl_id',
        'reminded'
    ];

    // public static function whereSchoolProgramId($id)
    // {
    //     if (is_array($id) && empty($id)) return new Collection;

    //     $instance = new static;

    //     return $instance->newQuery()->where('id', $id)->first();
    // }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    // Partner as Corporate
    public function partner()
    {
        return $this->belongsTo(Corporate::class, 'corp_id', 'corp_id');
    }

    // Partner PIC as Corporate PIC
    public function partnerPic()
    {
        return $this->belongsTo(CorporatePic::class, 'corp_pic', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'empl_id', 'id');
    }
}
