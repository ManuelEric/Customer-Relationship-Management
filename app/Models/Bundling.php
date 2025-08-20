<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BundlingDetail> $details
 * @property-read int|null $details_count
 * @property-read \App\Models\BundlingDetail|null $first_detail
 * @property-read \App\Models\InvoiceProgram|null $invoice_b2c
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bundling newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bundling newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bundling query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bundling whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bundling whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bundling whereUuid($value)
 *
 * @mixin \Eloquent
 */
class Bundling extends Model
{
    use HasFactory;

    protected $table = 'tbl_bundling';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
    ];

    /**
     * Get the invoice b2c for the bundling
     */
    public function invoice_b2c(): HasOne
    {
        return $this->hasOne(InvoiceProgram::class, 'bundling_id', 'uuid');
    }

    public function details(): HasMany
    {
        return $this->hasMany(BundlingDetail::class, 'bundling_id', 'uuid');
    }

    public function first_detail()
    {
        return $this->hasOne(BundlingDetail::class, 'bundling_id', 'uuid')->oldestOfMany();
    }
}
