<?php

namespace App\Models\Mentoring;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class MentoringLog extends Model
{
    protected $connection = 'mysql_mentoring';
    protected $table = 'mentoring_logs';

}
