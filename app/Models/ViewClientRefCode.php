<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string $full_name
 * @property string|null $ref_code
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientRefCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientRefCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientRefCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientRefCode whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientRefCode whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientRefCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientRefCode whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ViewClientRefCode whereRefCode($value)
 *
 * @mixin \Eloquent
 */
class ViewClientRefCode extends Model
{
    use HasFactory;

    protected $table = 'client_ref_code_view';
}
