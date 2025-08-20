<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $mg_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MajorGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MajorGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MajorGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MajorGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MajorGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MajorGroup whereMgName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MajorGroup whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class MajorGroup extends Model
{
    protected $fillable = [
        'mg_name',
    ];
}
