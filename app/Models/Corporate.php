<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corporate extends Model
{
    use HasFactory;

    protected $table = 'tbl_corp';
    protected $primaryKey = 'corp_id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'corp_id',
        'corp_name',
        'corp_industry',
        'corp_mail',
        'corp_phone',
        'corp_insta',
        'corp_site',
        'corp_region',
        'corp_address',
        'corp_note',
        'corp_password',
        'country_type',
        'type',
        'partnership_type',
    ];

    public static function whereCorpId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->where('corp_id', $id)->first();
    }

    public function edufair()
    {
        return $this->hasMany(EdufLead::class, 'corp_id', 'corp_id');
    }

    public function pic()
    {
        return $this->hasMany(CorporatePic::class, 'corp_id', 'corp_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'tbl_corp_partner_event', 'corp_id', 'event_id');
    }

    public function clientProgram()
    {
        return $this->hasMany(ClientProgram::class, 'partner_id', 'corp_id');
    }

    public function clientEvent()
    {
        return $this->hasMany(ClientEvent::class, 'partner_id', 'corp_id');
    }

    public function partnerProgram()
    {
        return $this->hasMany(partnerProg::class, 'corp_id', 'corp_id');
    }
}
