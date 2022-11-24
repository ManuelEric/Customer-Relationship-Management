<?php

namespace App\Interfaces;

interface UniversityPicRepositoryInterface 
{
    public function getAllUniversityPicByUniversityId($universityId);
    public function getUniversityPicById($picId);
    public function deleteUniversityPic($picId);
    public function createUniversityPic(array $picDetails);
    public function updateUniversityPic($picId, array $newDetails);
}