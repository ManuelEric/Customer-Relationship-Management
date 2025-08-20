<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $clientprog_id
 * @property int $sent_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClientProgram $clientProgram
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramLogMail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramLogMail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramLogMail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramLogMail whereClientprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramLogMail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramLogMail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramLogMail whereSentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientProgramLogMail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ClientProgramLogMail extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_prog_log_mail';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'clientprog_id',
        'sent_status',
    ];

    public function clientProgram()
    {
        return $this->belongsTo(ClientProgram::class, 'clientprog_id', 'clientprog_id');
    }
}
