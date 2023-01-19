<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Invb2b extends Model
{
    use HasFactory;

    protected $table = 'tbl_invb2b';
    protected $primaryKey = 'invb2b_num';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'invb2b_id',
        'schprog_id',
        'partnerprog_id',
        'invb2b_price',
        'invb2b_priceidr',
        'invb2b_participants',
        'invb2b_disc',
        'invb2b_discidr',
        'invb2b_totprice',
        'invb2b_totpriceidr',
        'invb2b_words',
        'invb2b_wordsidr',
        'invb2b_date',
        'invb2b_duedate',
        'invb2b_pm',
        'invb2b_notes',
        'invb2b_tnc',
        'invb2b_status',
        'curs_rate',
        'currency',
    ];

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public function inv_detail()
    {
        return $this->hasMany(InvDetail::class, 'invb2b_id', 'invb2b_id');
    }
  
    public function receipt()
    {
        return $this->hasMany(Receipt::class, 'invb2b_id', 'invb2b_id');
    }

    public function refund()
    {
        return $this->hasOne(Refund::class, 'invb2b_id', 'invb2b_id');
    }

}
