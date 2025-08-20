<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $client_id
 * @property string $category
 * @property string $value
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\UserClient $client
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserClientAdditionalInfo whereValue($value)
 *
 * @mixin \Eloquent
 */
class UserClientAdditionalInfo extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_additional_info';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'category',
        'value',
        'description',
    ];

    public function client()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id');
    }
}
