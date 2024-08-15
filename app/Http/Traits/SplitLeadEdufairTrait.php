<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

trait SplitLeadEdufairTrait {

    public function splitLeadEdufair(string $id)
    {
        $exploded = explode('-', $id);

        $lead_id = $exploded[0];
        $eduf_id = $exploded[1];

        return [
            'lead_id' => $lead_id,
            'eduf_id' => $eduf_id
        ];
    }
}