<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EdufReview extends Model
{
    use HasFactory;

    protected $table = 'tbl_eduf_review';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'eduf_id',
        'reviewer_name',
        'score',
        'review',
    ];

    public function edufair()
    {
        return $this->belongsTo(EdufLead::class, 'eduf_id', 'id');
    }
}
