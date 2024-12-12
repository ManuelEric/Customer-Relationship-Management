<?php

namespace App\Interfaces;

interface SubSectorRepositoryInterface 
{
    public function rnGetAllSubSectors();
    public function rnGetSubSectorByIndustryId(int $industry_id);
}