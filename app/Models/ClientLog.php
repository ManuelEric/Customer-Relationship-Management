<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ClientLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_log';

    protected $fillable = [
        'client_id',
        'first_name',
        'last_name',
        'category',
        'lead_source',
        'inputted_from',
        'unique_key',
        'clientprog_id'
    ];

    public function update(array $attributes = [], array $options = [])
    {
        # set unique_key if null
        if(!isset($attributes['unique_key']) || $attributes['unique_key'] == null)
            $attributes['unique_key'] = Str::ulid()->toBase32();

        $updated = parent::update($attributes);

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        # set unique_key if null
        if(!isset($attributes['unique_key']) || $attributes['unique_key'] == null)
            $attributes['unique_key'] = Str::ulid()->toBase32();

        $model = static::query()->create($attributes);

        return $model;
    }

    /**
     * Attribute 
     */
    public function formattedCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($this->created_at)),
        );
    }

    public function formattedUpdatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('M d, Y H:i:s', strtotime($this->updated_at)),
        );
    }

    /**
     * The scopes.
     */
    public function scopeRawLeads(Builder $query): void
    {
        $query->where('category', 'raw');
    }

    public function scopeNewLeads(Builder $query): void
    {
        $query->where('category', 'new-lead');
    }

    public function scopePotentialLeads(Builder $query): void
    {
        //! as from the discussion, we need to group by the potential leads in order to remove redundant data
        //! for example, if client has been offered 3 programs at the time then the data display should be 1
        $query->where('category', 'potential')->groupBy('client_id');
    }

    public function scopePotentialLeadsByProduct(Builder $query): void
    {
        $query->where('category', 'potential');
    }

    public function scopeDealLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->
            whereIn('category', ['mentee', 'non-mentee'])->
            whereHas('client_program', function ($sub) use ($start_date, $end_date) {
                $sub->whereBetween('success_date', [$start_date, $end_date]);
            });
    }

    public function scopeHasAgreement(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->whereHas('client_program', function ($sub)  use ($start_date, $end_date) {
            $sub->whereNotNull('agreement')->whereBetween('agreement_uploaded_at', [$start_date, $end_date]);
        });
    }

    public function scopeAlreadyPaidTheProgram(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->whereHas('client_program.invoice.firstReceipt', function ($sub) use ($start_date, $end_date) {
            $sub->whereBetween('receipt_date', [$start_date, $end_date]);
        });
    }

    public function scopeOnlinePaid(Builder $query): void
    {
        $query->whereHas('lead_source_log', function ($sub) {
            $sub->where('type', 'paid')->where('is_online', true);
        });
    }

    public function scopeOnlineOrganic(Builder $query): void
    {
        $query->whereHas('lead_source_log', callback: function ($sub) {
            $sub->where('type', 'organic')->where('is_online', true);
        });
    }

    public function scopeOffline(Builder $query): void
    {
        $lead_of_referral = ['LS005', 'LS058', 'LS060', 'LS061'];
        $query->whereHas('lead_source_log', function ($sub) use ($lead_of_referral) {
            $sub->where('is_online', false)->whereNotIn('lead_id', $lead_of_referral);
        });
    }


    public function scopeReferral(Builder $query): void
    {
        $lead_of_referral = ['LS005', 'LS058', 'LS060', 'LS061']; # manually select lead from referral
        $query->whereHas('lead_source_log', function ($sub) use ($lead_of_referral) {
            $sub->whereIn('lead_id', $lead_of_referral);
        });
    }


    public function scopeMentoring(Builder $query): void
    {
        $query->whereHas('client_program.program.main_prog', function ($sub) {
            $sub->where('prog_name', 'Admissions Mentoring');
        });
    }


    public function scopeTutoring(Builder $query): void
    {
        $query->whereHas('client_program.program.main_prog', function ($sub) {
            $sub->where('prog_name', 'Academic & Test Preparation');
        });
    }


    public function scopeGIP(Builder $query): void
    {
        $query->whereHas('client_program.program.sub_prog', function ($sub) {
            $sub->where('sub_prog_name', 'Global Immersion Program');
        });
    }


    public function scopeTookAssessment(Builder $query, Carbon $start_date, Carbon $end_date): void 
    {
        $query->whereHas('master_client', function ($sub) use ($start_date, $end_date) {
            $sub->where('took_ia', 1)->whereBetween('took_ia_date', [$start_date, $end_date]);
        });
    }


    public function scopeInitialConsult(Builder $query): void
    {
        $query->whereHas('client_program', function ($sub)  {
            // $sub->whereBetween('initconsult_date', [$start_date, $end_date]);
            $sub->whereNotNull('initconsult_date');
        });
    }


    public function scopeInitialAssessment(Builder $query): void
    {
        $query->whereHas('client_program', function ($sub) {
            // $sub->whereBetween('assessmentsent_date', [$start_date, $end_date]);
            $sub->whereNotNull('assessmentsent_date');
        });
    }


    public function scopeTrialDate(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->whereHas('client_program', function ($query) use ($start_date, $end_date)  {
            $query->whereBetween('trial_date', [$start_date, $end_date]);
        });
    }


    public function scopeOnlinePaidUnfilteredLeads(Builder $query): void
    {
        $query->onlinePaid()->rawLeads();
    }

    public function scopeOnlineOrganicUnfilteredLeads(Builder $query): void
    {
        $query->onlineOrganic()->rawLeads();
    }

    public function scopeOfflineUnfilteredLeads(Builder $query): void
    {
        $query->offline()->rawLeads();
    }

    public function scopeReferralFromExistingClientsUnfilteredLeads(Builder $query): void
    {
        $query->referral()->rawLeads();
    }

    public function scopeOnlinePaidFilteredLeads(Builder $query): void
    {
        $query->onlinePaid()->newLeads();
    }

    public function scopeOnlineOrganicFilteredLeads(Builder $query): void
    {
        $query->onlineOrganic()->newLeads();
    }

    public function scopeOfflineFilteredLeads(Builder $query): void
    {
        $query->offline()->newLeads();
    }

    public function scopeReferralFromExistingClientsFilteredLeads(Builder $query): void
    {
        $query->referral()->newLeads();
    }

    public function scopeOnlinePaidPotentialLeads(Builder $query): void
    {
        $query->onlinePaid()->potentialLeads();
    }

    public function scopeOnlineOrganicPotentialLeads(Builder $query): void
    {
        $query->onlineOrganic()->potentialLeads();
    }

    public function scopeOfflinePotentialLeads(Builder $query): void
    {
        $query->offline()->potentialLeads();
    }

    public function scopeReferralFromExistingClientsPotentialLeads(Builder $query): void
    {
        $query->referral()->potentialLeads();
    }

    public function scopeOnlinePaidDealLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->onlinePaid()->dealLeads($start_date, $end_date);
    }

    public function scopeOnlineOrganicDealLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->onlineOrganic()->dealLeads($start_date, $end_date);
    }

    public function scopeOfflineDealLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->offline()->dealLeads($start_date, $end_date);
    }

    public function scopeReferralFromExistingClientsDealLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->referral()->dealLeads($start_date, $end_date);
    }

    public function scopeOnlinePaidAgreement(Builder $query): void
    {
        $query->onlinePaid()->hasAgreement();
    }

    public function scopeOnlineOrganicAgreement(Builder $query): void
    {
        $query->onlineOrganic()->hasAgreement();
    }

    public function scopeOfflineAgreement(Builder $query): void
    {
        $query->offline()->hasAgreement();
    }

    public function scopeReferralFromExistingClientsAgreement(Builder $query): void
    {
        $query->referral()->hasAgreement();
    }

    public function scopeOnlinePaidPaymentLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->onlinePaid()->alreadyPaidTheProgram($start_date, $end_date)->groupBy('clientprog_id');
    }

    public function scopeOnlineOrganicPaymentLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->onlineOrganic()->alreadyPaidTheProgram($start_date, $end_date)->groupBy('clientprog_id');
    }

    public function scopeOfflinePaymentLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->offline()->alreadyPaidTheProgram($start_date, $end_date)->groupBy('clientprog_id');
    }

    public function scopeReferralFromExistingClientsPaymentLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->referral()->alreadyPaidTheProgram($start_date, $end_date)->groupBy('clientprog_id');
    }



    /**
     * The relations.
     */
    public function master_client()
    {
        return $this->belongsTo(UserClient::class, 'client_id', 'id')->withTrashed();
    }

    public function lead_source_log()
    {
        return $this->belongsTo(Lead::class, 'lead_source', 'lead_id');
    }

    public function client_program()
    {
        return $this->belongsTo(ClientProgram::class, 'clientprog_id', 'clientprog_id');
    }
}
