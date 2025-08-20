<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $client_id
 * @property string|null $full_name
 * @property string|null $graduation_year
 * @property string|null $waitlisted_groups
 * @property string|null $accepted_groups
 * @property string|null $denied_groups
 * @property string|null $chosen_groups
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereAcceptedGroups($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereChosenGroups($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereDeniedGroups($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereGraduationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientAcceptance whereWaitlistedGroups($value)
 *
 * @mixin \Eloquent
 */
class ClientAcceptance extends Model
{
    use HasFactory;

    protected $table = 'client_acceptance';
}
