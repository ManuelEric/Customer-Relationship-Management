<?php

namespace App\Models\pivot;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSubject extends Pivot
{
    protected $table = 'tbl_user_subjects';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_role_id', 
        'subject_id', 
        'fee_hours',
        'fee_session',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

}
