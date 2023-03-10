<?php

namespace App\Interfaces;

interface ReasonRepositoryInterface
{

    // public function getReasonBySchoolProgramId($schoolProgramId);
    public function getAllReasons();
    public function getReasonById($reasonId);
    public function getReasonByReasonName($reasonName);
    public function deleteReason($reasonId);
    public function createReason(array $reasons);
    public function updateReason($reasonId, array $reasons);
}
