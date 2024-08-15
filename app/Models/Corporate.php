<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'created_at',
        'updated_at',
    ];

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_partner', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_partner', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_partner', 'channel_datatable'));

        return $model;
    }


    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($value)),
        );
    }

    public static function whereCorpId($id)
    {
        if (is_array($id) && empty($id)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->where('corp_id', $id)->first();
    }

    public static function whereCorpName($name)
    {
        if (is_array($name) && empty($name)) return new Collection;

        $instance = new static;

        return $instance->newQuery()->whereRaw('lower(corp_name) = ?', [$name])->first();
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
        return $this->hasMany(PartnerProg::class, 'corp_id', 'corp_id');
    }

    public function referralProgram()
    {
        return $this->hasMany(Referral::class, 'partner_id', 'corp_id');
    }

    public function asCollaboratorInPartnerProgram()
    {
        return $this->belongsToMany(PartnerProg::class, 'tbl_partner_prog_partner', 'corp_id', 'partnerprog_id')->withTimestamps();
    }

    public function asCollaboratorInSchoolProgram()
    {
        return $this->belongsToMany(SchoolProg::class, 'tbl_sch_prog_partner', 'corp_id', 'schprog_id')->withTimestamps();
    }
}
