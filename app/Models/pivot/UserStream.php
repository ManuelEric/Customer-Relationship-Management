<?php

namespace App\Models\pivot;

use App\Models\PhaseDetail;
use App\Models\Stream;
use Illuminate\Database\Eloquent\Model;

class UserStream extends Model
{
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_role_id', 
        'stream_id', 
        'engagement_type_id',
        'package',
        'fee_individual',
        'grade',
        'additional_fee',
        'agreement',
        'head',
        'start_date',
        'end_date',
    ];

    public function stream()
    {
        return $this->belongsTo(Stream::class, 'stream_id', 'id');
    }

    public function user_roles()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id', 'id')->with('role');
    }

    public function engagement_type()
    {
        return $this->belongsTo(PhaseDetail::class, 'engagement_type_id', 'id');
    }
}
