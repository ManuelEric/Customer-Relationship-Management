<?php

namespace App\Models;

use App\Events\MessageSent;
use App\Models\pivot\AgendaSpeaker;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProg extends Model
{
    use HasFactory;

    protected $table = 'tbl_partner_prog';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'corp_id',
        'prog_id',
        'type',
        'first_discuss',
        'notes',
        'status',
        'participants',
        'start_date',
        'end_date',
        'is_corporate_scheme',
        'reason_id',
        'total_fee',
        'success_date',
        'pending_date',
        'accepted_date',
        'cancel_date',
        'refund_date',
        'denied_date',
        'refund_notes',
        'empl_id',
    ];

    # Modify methods Model
    public function delete()
    {
        // Custom logic before deleting the model

        parent::delete();

        // Custom logic after deleting the model
        // Send to pusher
        event(new MessageSent('rt_partner_program', 'channel_datatable'));

        return true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        // Custom logic before update

        $updated = parent::update($attributes);

        // Custom logic after update
        // Send to pusher
        event(new MessageSent('rt_partner_program', 'channel_datatable'));

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // Custom logic before creating the model

        $model = static::query()->create($attributes);

        // Custom logic after creating the model

        // Send to pusher
        event(new MessageSent('rt_partner_program', 'channel_datatable'));

        return $model;
    }

    public function firstDiscuss(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y', strtotime($value)),
        );
    }

    public function school_speaker()
    {
        return $this->belongsToMany(SchoolDetail::class, 'tbl_agenda_speaker', 'partner_prog_id', 'sch_pic_id')->using(AgendaSpeaker::class);
    }

    public function partner_speaker()
    {
        return $this->belongsToMany(CorporatePic::class, 'tbl_agenda_speaker', 'partner_prog_id', 'partner_pic_id')->using(AgendaSpeaker::class);
    }

    public function internal_speaker()
    {
        return $this->belongsToMany(User::class, 'tbl_agenda_speaker', 'partner_prog_id', 'empl_id')->using(AgendaSpeaker::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'prog_id', 'prog_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'empl_id', 'id');
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class, 'reason_id', 'reason_id');
    }

    public function corp()
    {
        return $this->belongsTo(Corporate::class, 'corp_id', 'corp_id');
    }

    public function invoiceB2b()
    {
        return $this->belongsTo(Invb2b::class, 'id', 'partnerprog_id');
    }

    public function schoolCollaborators()
    {
        return $this->belongsToMany(School::class, 'tbl_partner_prog_sch', 'partnerprog_id', 'sch_id')->withTimestamps();
    }

    public function univCollaborators()
    {
        return $this->belongsToMany(University::class, 'tbl_partner_prog_univ', 'partnerprog_id', 'univ_id')->withTimestamps();
    }

    public function partnerCollaborators()
    {
        return $this->belongsToMany(Corporate::class, 'tbl_partner_prog_partner', 'partnerprog_id', 'corp_id')->withTimestamps();
    }

}
