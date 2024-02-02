<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


class SalesTarget extends Model
{
    use HasFactory;

    protected $table = 'tbl_sales_target';
    protected $primaryKey = 'id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'main_prog_id',
        'prog_id',
        'sub_prog_id',
        'month_year',
        'total_participant',
        'total_target',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

}
