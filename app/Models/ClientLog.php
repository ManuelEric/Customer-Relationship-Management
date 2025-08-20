<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $client_id
 * @property string $first_name
 * @property string|null $last_name
 * @property string $category
 * @property string|null $utm_content
 * @property string|null $lead_source
 * @property string $inputted_from manually input or through import
 * @property string $unique_key
 * @property int|null $clientprog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\ClientProgram|null $client_program
 * @property-read mixed $formatted_created_at
 * @property-read mixed $formatted_updated_at
 * @property-read \App\Models\Lead|null $lead_source_log
 * @property-read \App\Models\UserClient $master_client
 *
 * @method static Builder<static>|ClientLog alreadyPaidTheProgram(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog dealLeads(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog gIP()
 * @method static Builder<static>|ClientLog hasAgreement(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog initialAssessment()
 * @method static Builder<static>|ClientLog initialConsult()
 * @method static Builder<static>|ClientLog mentoring()
 * @method static Builder<static>|ClientLog newLeads()
 * @method static Builder<static>|ClientLog newModelQuery()
 * @method static Builder<static>|ClientLog newQuery()
 * @method static Builder<static>|ClientLog offline()
 * @method static Builder<static>|ClientLog offlineAgreement()
 * @method static Builder<static>|ClientLog offlineDealLeads(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog offlineFilteredLeads()
 * @method static Builder<static>|ClientLog offlinePaymentLeads(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog offlinePotentialLeads()
 * @method static Builder<static>|ClientLog offlineUnfilteredLeads()
 * @method static Builder<static>|ClientLog onlineOrganic()
 * @method static Builder<static>|ClientLog onlineOrganicAgreement()
 * @method static Builder<static>|ClientLog onlineOrganicDealLeads(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog onlineOrganicFilteredLeads()
 * @method static Builder<static>|ClientLog onlineOrganicPaymentLeads(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog onlineOrganicPotentialLeads()
 * @method static Builder<static>|ClientLog onlineOrganicUnfilteredLeads()
 * @method static Builder<static>|ClientLog onlinePaid()
 * @method static Builder<static>|ClientLog onlinePaidAgreement()
 * @method static Builder<static>|ClientLog onlinePaidDealLeads(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog onlinePaidFilteredLeads()
 * @method static Builder<static>|ClientLog onlinePaidPaymentLeads(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog onlinePaidPotentialLeads()
 * @method static Builder<static>|ClientLog onlinePaidUnfilteredLeads()
 * @method static Builder<static>|ClientLog potentialLeads()
 * @method static Builder<static>|ClientLog potentialLeadsByProduct()
 * @method static Builder<static>|ClientLog query()
 * @method static Builder<static>|ClientLog rawLeads()
 * @method static Builder<static>|ClientLog referral()
 * @method static Builder<static>|ClientLog referralFromExistingClientsAgreement()
 * @method static Builder<static>|ClientLog referralFromExistingClientsDealLeads(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog referralFromExistingClientsFilteredLeads()
 * @method static Builder<static>|ClientLog referralFromExistingClientsPaymentLeads(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog referralFromExistingClientsPotentialLeads()
 * @method static Builder<static>|ClientLog referralFromExistingClientsUnfilteredLeads()
 * @method static Builder<static>|ClientLog search(?array $search = [])
 * @method static Builder<static>|ClientLog tookAssessment(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog trialDate(\Illuminate\Support\Carbon $start_date, \Illuminate\Support\Carbon $end_date)
 * @method static Builder<static>|ClientLog tutoring()
 * @method static Builder<static>|ClientLog whereCategory($value)
 * @method static Builder<static>|ClientLog whereClientId($value)
 * @method static Builder<static>|ClientLog whereClientprogId($value)
 * @method static Builder<static>|ClientLog whereCreatedAt($value)
 * @method static Builder<static>|ClientLog whereFirstName($value)
 * @method static Builder<static>|ClientLog whereId($value)
 * @method static Builder<static>|ClientLog whereInputtedFrom($value)
 * @method static Builder<static>|ClientLog whereLastName($value)
 * @method static Builder<static>|ClientLog whereLeadSource($value)
 * @method static Builder<static>|ClientLog whereUniqueKey($value)
 * @method static Builder<static>|ClientLog whereUpdatedAt($value)
 * @method static Builder<static>|ClientLog whereUtmContent($value)
 *
 * @mixin \Eloquent
 */
class ClientLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_client_log';

    // CLIENT LOG MODEL
    protected $fillable = [
        'client_id',
        'first_name',
        'last_name',
        'category',
        'utm_content',
        'lead_source',
        'utm_content',
        'inputted_from',
        'unique_key',
        'clientprog_id',
    ];

    public function update(array $attributes = [], array $options = [])
    {
        // set unique_key if null
        if (! isset($attributes['unique_key']) || $attributes['unique_key'] == null) {
            $attributes['unique_key'] = Str::ulid()->toBase32();
        }

        $updated = parent::update($attributes);

        return $updated;
    }

    public static function create(array $attributes = [])
    {
        // set unique_key if null
        if (! isset($attributes['unique_key']) || $attributes['unique_key'] == null) {
            $attributes['unique_key'] = Str::ulid()->toBase32();
        }

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
        // ! as from the discussion, we need to group by the potential leads in order to remove redundant data
        // ! for example, if client has been offered 3 programs at the time then the data display should be 1
        $query->where('category', 'potential')->groupBy('client_id');
    }

    public function scopePotentialLeadsByProduct(Builder $query): void
    {
        $query->where('category', 'potential');
    }

    public function scopeDealLeads(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->whereIn('category', ['mentee', 'non-mentee'])->whereHas('client_program', function ($sub) use ($start_date, $end_date) {
            $sub->whereBetween('success_date', [$start_date, $end_date]);
        });
    }

    public function scopeHasAgreement(Builder $query, Carbon $start_date, Carbon $end_date): void
    {
        $query->whereHas('client_program', function ($sub) use ($start_date, $end_date) {
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
        $lead_of_referral = ['LS005', 'LS058', 'LS060', 'LS061']; // manually select lead from referral
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
            $sub->where('group_of', 'Tutoring');
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
        $query->whereHas('client_program', function ($sub) {
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
        $query->whereHas('client_program', function ($query) use ($start_date, $end_date) {
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

    public function scopeSearch(Builder $query, ?array $search = [])
    {
        $query->when($search, function ($query) use ($search) {
            $query->whereHas('master_client', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->when(isset($search['utm_content']), function ($query) use ($search) {
                        $query
                            ->where('utm_content', 'like', '%'.$search['utm_content'].'%');
                    });
                    $query->when(isset($search['lead_source']), function ($query) use ($search) {
                        $query
                            ->whereHas('lead', function ($query) use ($search) {
                                $query->where('main_lead', 'like', '%'.$search['lead_source'].'%');
                            });
                    });
                    $query->when(isset($search['search']), function ($query) use ($search) {
                        $query
                            ->where('first_name', 'like', '%'.$search['search'].'%')
                            ->orWhere('last_name', 'like', '%'.$search['search'].'%')
                            ->orWhere('mail', 'like', '%'.$search['search'].'%');
                    });
                });
            })->orWhereHas('client_program', function ($query) use ($search) {
                $query->when(isset($search['lead_source']), function ($query) use ($search) {
                    $query
                        ->whereHas('lead', function ($query) use ($search) {
                            $query->where('main_lead', 'like', '%'.$search['lead_source'].'%');
                        });
                });
                $query->when(isset($search['search']), function ($query) use ($search) {
                    $query->whereHas('program', function ($query) use ($search) {
                        $query->where('prog_program', 'like', '%'.$search['search'].'%');
                    });
                });
            });
        });
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
