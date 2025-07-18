<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Model;

class EditorAgreement extends Model
{
    protected $fillable = [
        'id',
        'user_role_id', 
        'category',
        'fee_individual',
        'agreement',
        'start_date',
        'end_date',
    ];

    public function user_roles()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id', 'id')->with('role');
    }
}
