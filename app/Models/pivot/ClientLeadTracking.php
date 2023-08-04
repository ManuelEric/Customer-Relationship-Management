<?php

namespace App\Models\pivot;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientLeadTracking extends Pivot
{

    protected $table = 'tbl_client_lead_tracking';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'initialprogram_id',
        'type',
        'total_result',
        'status',
        'reason_id'
    ];

    public function leadStatus(): Attribute
    {
        if ($this->type == 'Lead') {

            if($this->total_result >= 0.65){
                return Attribute::make(
                    get: fn ($value) => 'Hot',
                );
            }else if($this->total_result >= 0.35 && $this->total_result < 0.65){
                return Attribute::make(
                    get: fn ($value) => 'Warm',
                );
                
            }else if($this->total_result < 0.35){
                return Attribute::make(
                    get: fn ($value) => 'Cold',
                );
            }

        } else {
            return Attribute::make(
                get: fn ($value) => null,
            );
        }
    }
}
