<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedMetaLead extends Model
{
    protected $fillable = [
        'parent_name',
        'parent_phone',
        'parent_email',
        'child_name',
        'child_graduation_year',
        'child_school',
    ];
}
