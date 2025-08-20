<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $bundling_id
 * @property int $clientprog_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bundling $bundling
 * @property-read \App\Models\ClientProgram $client_program
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BundlingDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BundlingDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BundlingDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BundlingDetail whereBundlingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BundlingDetail whereClientprogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BundlingDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BundlingDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BundlingDetail whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class BundlingDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_bundling_dtl';

    protected $primaryKey = 'id';

    public $incrementing = true;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'bundling_id',
        'clientprog_id',
    ];

    /**
     * Get the bundling of the details
     */
    public function bundling()
    {
        return $this->belongsTo(Bundling::class, 'bundling_id', 'uuid');
    }

    public function client_program()
    {
        return $this->belongsTo(ClientProgram::class, 'clientprog_id', 'clientprog_id');
    }
}
