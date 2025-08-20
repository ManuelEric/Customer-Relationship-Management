<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $corp_id
 * @property string $agreement_name
 * @property int $agreement_type 0: Referral Mutual Agreement, 1: Partnership Agreement, 2: Speaker Agreement, 3: University Agent
 * @property string $attachment
 * @property string $start_date
 * @property string $end_date
 * @property int $corp_pic
 * @property string $empl_id
 * @property int $reminded
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Corporate $partner
 * @property-read \App\Models\CorporatePic $partnerPic
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereAgreementName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereAgreementType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereCorpPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereEmplId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereReminded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerAgreement whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PartnerAgreement extends Model
{
    use HasFactory;

    protected $table = 'tbl_partner_agreement';

    protected $primaryKey = 'id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
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
        'reminded',
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
