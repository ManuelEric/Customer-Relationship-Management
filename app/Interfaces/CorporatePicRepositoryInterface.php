<?php

namespace App\Interfaces;

interface CorporatePicRepositoryInterface
{
    public function getAllCorporatePicByCorporateId($corporateId);
    public function getCorporatePicById($picId);
    public function deleteCorporatePic($picId);
    public function createCorporatePic(array $picDetails);
    public function updateCorporatePic($picId, array $newDetails);
}