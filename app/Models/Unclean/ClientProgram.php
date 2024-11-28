<?php

namespace App\Models\Unclean;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProgram extends Model
{
    use HasFactory;

    protected $connection = 'mysql_bigdatav1';

    protected $table = 'tbl_client_prog';
    protected $primaryKey = 'id';
    

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        // 'client_uuid',
        'prog_id',
        'lead_id',
        'eduf_lead_id',
        'partner_id',
        'clientevent_id',
        'first_discuss_date',
        'last_discuss_date',
        'followup_date',
        'meeting_date',
        'meeting_notes',
        'status',
        'statusprog_date',
        'initconsult_date',
        'assessmentsent_date',
        'negotiation_date',
        'reason_id',
        'test_date',
        'first_class',
        'last_class',
        'diag_score',
        'test_score',
        'price_from_tutor',
        'our_price_tutor',
        'total_price_tutor',
        'duration_notes',
        'total_uni',
        'total_foreign_currency',
        'foreign_currency_exchange',
        'foreign_currency',
        'total_idr',
        'installment_notes',
        'prog_running_status',
        'prog_start_date',
        'prog_end_date',
        'empl_id',
        'success_date',
        'failed_date',
        'refund_date',
        'refund_notes',
        'timesheet_link',
        'trial_date',
        'session_tutor',
        'registration_type',
        'referral_code',
        'agreement',
        'created_at',
        'updated_at'
    ];

   
}
