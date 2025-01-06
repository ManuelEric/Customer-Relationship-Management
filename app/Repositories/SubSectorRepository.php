<?php

namespace App\Repositories;

use App\Interfaces\SubSectorRepositoryInterface;
use App\Models\SubSector;

class SubSectorRepository implements SubSectorRepositoryInterface
{

    public function rnGetAllSubSectors()
    {
        return SubSector::all();
    }

    public function rnGetSubSectorByIndustryId(int $industry_id)
    {
        return SubSector::where('industry_id', $industry_id)->get();
    }
}