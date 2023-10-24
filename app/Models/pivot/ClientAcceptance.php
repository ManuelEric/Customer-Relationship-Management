<?php

namespace App\Models\pivot;

use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientAcceptance extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'univ_id',
        'major_id',
        'status'
    ];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id', 'id');
    }
}
