<?php

namespace App\Models\pivot;

use App\Models\Subject;
use App\Models\Pivot\UserRole;
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
        'fee_individual',
        'fee_group',
        'grade',
        'additional_fee',
        'agreement',
        'head',
        'year'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function user_roles()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id', 'id');
    }

}
