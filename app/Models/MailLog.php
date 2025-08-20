<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $identifier
 * @property string $category ex: invoice / receipt
 * @property string $target ex: partner / client / etc
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailLog whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailLog whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailLog whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MailLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class MailLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_mail_log';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'identifier',
        'category',
        'target',
        'description',
    ];
}
