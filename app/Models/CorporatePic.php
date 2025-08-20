<?php

namespace App\Models;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $corp_id
 * @property string $pic_name
 * @property string|null $pic_mail
 * @property string|null $pic_linkedin
 * @property string|null $pic_phone
 * @property int $is_pic
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read AgendaSpeaker|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $as_event_speaker
 * @property-read int|null $as_event_speaker_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SchoolProg> $as_schoolprog_speaker
 * @property-read int|null $as_schoolprog_speaker_count
 * @property-read \App\Models\Corporate $corporate
 * @property-read \App\Models\PartnerAgreement|null $partner_agreement
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic whereCorpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic whereIsPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic wherePicLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic wherePicMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic wherePicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic wherePicPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CorporatePic whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class CorporatePic extends Model
{
    use HasFactory;

    protected $table = 'tbl_corp_pic';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'corp_id',
        'pic_name',
        'pic_mail',
        'pic_linkedin',
        'pic_phone',
        'is_pic',
    ];

    public function corporate()
    {
        return $this->belongsTo(Corporate::class, 'corp_id', 'corp_id');
    }

    public function as_event_speaker()
    {
        return $this->belongsToMany(Event::class, 'tbl_agenda_speaker', 'partner_pic_id', 'event_id')->using(AgendaSpeaker::class);
    }

    public function as_schoolprog_speaker()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_agenda_speaker', 'partner_pic_id', 'sch_prog_id')->using(AgendaSpeaker::class);
    }

    public function partner_agreement()
    {
        return $this->hasOne(PartnerAgreement::class, 'corp_pic', 'id');
    }
}
