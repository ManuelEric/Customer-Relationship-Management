<?php

namespace App\Models;

use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $univ_id
 * @property string $name
 * @property string $title
 * @property string|null $phone
 * @property string|null $email
 * @property int $is_pic
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read AgendaSpeaker|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $asSpeaker
 * @property-read int|null $as_speaker_count
 * @property-read \App\Models\University $university
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic whereIsPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic whereUnivId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UniversityPic whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UniversityPic extends Model
{
    use HasFactory;

    protected $table = 'tbl_univ_pic';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'univ_id',
        'name',
        'title',
        'phone',
        'email',
        'is_pic',
    ];

    public function university()
    {
        return $this->belongsTo(University::class, 'univ_id', 'univ_id');
    }

    public function asSpeaker()
    {
        return $this->belongsToMany(Event::class, 'tbl_agenda_speaker', 'univ_pic_id', 'event_id')->using(AgendaSpeaker::class);
    }
}
