<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProgAttach extends Model
{
    use HasFactory;

    protected $table = 'tbl_partner_prog_attachment';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'partner_prog_id',
        'corprog_file',
        'corprog_attach',
    ];

    public function partner_program()
    {
        return $this->belongsTo(PartnerProg::class, 'partner_prog_id', 'id');
    }
}
