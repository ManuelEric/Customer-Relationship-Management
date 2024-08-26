<?php

namespace App\Repositories;

use App\Interfaces\CorporatePicRepositoryInterface;
use App\Models\CorporatePic;

class CorporatePicRepository implements CorporatePicRepositoryInterface
{
    public function getAllCorporatePicByCorporateId($corporateId)
    {
        return CorporatePic::where('corp_id', $corporateId)->get();
    }

    public function getAllCorporatePic()
    {
        return CorporatePic::orderBy('pic_name', 'asc')->get();
    }

    public function getCorporatePicById($picId)
    {
        return CorporatePic::find($picId);
    }

    public function deleteAgendaSpeaker($corporateId, $eventId)
    {
        $speakers = CorporatePic::where('corp_id', $corporateId)->get();
        foreach ($speakers as $speaker) {
            $speaker->as_event_speaker()->detach($eventId);
        }
    }

    public function deleteCorporatePic($picId)
    {
        return CorporatePic::destroy($picId);
    }

    public function createCorporatePic(array $picDetails)
    {
        return CorporatePic::create($picDetails);
    }

    public function updateCorporatePic($picId, array $newDetails)
    {
        return CorporatePic::whereId($picId)->update($newDetails);
    }
}
