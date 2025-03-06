<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;

    protected $table = 'phases';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'phase_name'
    ];

    public function phase_detail()
    {
        return $this->hasMany(PhaseDetail::class, 'phase_id', 'id');
    }
}
