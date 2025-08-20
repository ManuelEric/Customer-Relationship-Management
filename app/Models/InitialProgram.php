<?php

namespace App\Models;

use App\Models\pivot\ClientLeadTracking;
use App\Models\pivot\LeadBucket;
use App\Models\pivot\ProgramBucket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ProgramBucket|LeadBucket|ClientLeadTracking|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserClient> $client
 * @property-read int|null $client_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserClient> $clientLead
 * @property-read int|null $client_lead_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ParamLeads> $leadBucketParams
 * @property-read int|null $lead_bucket_params_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ParamLeads> $programBucketParams
 * @property-read int|null $program_bucket_params_count
 * @property-read \App\Models\SubProg|null $sub_prog
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InitialProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InitialProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InitialProgram query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InitialProgram whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InitialProgram whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InitialProgram whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InitialProgram whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class InitialProgram extends Model
{
    use HasFactory;

    protected $table = 'tbl_initial_program_lead';

    protected $primaryKey = 'id';

    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    public function sub_prog()
    {
        return $this->belongsTo(SubProg::class, 'sub_id', 'id');
        // return $this->belongsToMany(SubProg::class, 'tbl_initial_prog_sub_lead', 'initialprogram_id', 'subprogram_id');
    }

    public function client()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_lead_tracking', 'initialprogram_id', 'client_id')->using(ClientLeadTracking::class)->withTimestamps();
    }

    public function clientLead()
    {
        return $this->belongsToMany(UserClient::class, 'tbl_client_lead_tracking', 'initialprogram_id', 'client_id')->using(ClientLeadTracking::class)->withTimestamps();
    }

    public function programBucketParams()
    {
        return $this->belongsToMany(ParamLeads::class, 'tbl_program_buckets_params', 'initialprogram_id', 'param_id')->withPivot(['bucket_id',
            'initialprogram_id',
            'param_id',
            'weight_existing_non_mentee',
            'weight_existing_mentee',
            'weight_new'])->using(ProgramBucket::class)->withTimestamps();
    }

    public function leadBucketParams()
    {
        return $this->belongsToMany(ParamLeads::class, 'tbl_lead_bucket_params', 'initialprogram_id', 'param_id')->withPivot(['bucket_id',
            'initialprogram_id',
            'param_id',
            'weight_existing_non_mentee',
            'weight_existing_mentee',
            'weight_new'])->using(LeadBucket::class)->withTimestamps();
    }
}
