<?php

namespace App\Models\pivot;

use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientAcceptance extends Pivot
{
    use HasFactory;

    protected $table = 'tbl_client_acceptance';

    protected $fillable = [
        'client_id',
        'univ_id',
        'major_group',
        'major_id',
        'status',
        'is_picked',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id', 'id');
    }
}
