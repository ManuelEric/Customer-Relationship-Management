<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $partner_prog_id
 * @property string $corprog_file
 * @property string $corprog_attach
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PartnerProg $partner_program
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerProgAttach newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerProgAttach newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerProgAttach query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerProgAttach whereCorprogAttach($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerProgAttach whereCorprogFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerProgAttach whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerProgAttach whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerProgAttach wherePartnerProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PartnerProgAttach whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PartnerProgAttach extends Model
{
    use HasFactory;

    protected $table = 'tbl_partner_prog_attachment';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'partner_prog_id',
        'corprog_file',
        'corprog_attach',
    ];

    public function partner_program()
    {
        return $this->belongsTo(PartnerProg::class, 'partner_prog_id', 'id');
    }
}
