<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $foreign_identifier
 * @property string $content
 * @property int $sent_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereForeignIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereSentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reminder whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Reminder extends Model
{
    use HasFactory;

    protected $table = 'tbl_reminder';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'foreign_identifier',
        'content',
        'sent_status',
    ];
}
