<?php
namespace App\Http\Traits;

use App\Models\AgendaSpeaker;
use App\Models\Event;

trait FindAgendaSpeakerPriorityTrait {

    public function maxAgendaSpeakerPriority($class, $identifier, $agendaDetails)
    {

        switch ($class) {
            case "Event":
                $query = AgendaSpeaker::where('event_id', $identifier);
                break;

            case "School-Program":
                $query = AgendaSpeaker::where('sch_prog_id', $identifier);
                break;

            case "Partner-Program":
                $query = AgendaSpeaker::where('partner_prog_id', $identifier);
                break;
        }

        return $this->querySpeakerType($query, $agendaDetails);
    }

    public function querySpeakerType($query, $agendaDetails)
    {
        $speaker_type = $agendaDetails['speaker_type'];

        switch($speaker_type) {
            
            case "internal":
                return $query->whereNotNull('empl_id')->max('priority');
                break;

            case "partner":
                return $query->whereNotNull('partner_pic_id')->max('priority');
                break;

            case "school":
                return $query->whereNotNull('sch_pic_id')->max('priority');
                break;

            case "university":
                return $query->whereNotNull('univ_pic_id')->max('priority');
                break;

        }
        
    }
}