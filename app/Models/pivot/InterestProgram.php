<?php

namespace App\Models\pivot;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property string $client_id
 * @property string $prog_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Program $program
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InterestProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InterestProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InterestProgram query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InterestProgram whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InterestProgram whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InterestProgram whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InterestProgram whereProgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InterestProgram whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class InterestProgram extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_interest_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'status',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }
}
