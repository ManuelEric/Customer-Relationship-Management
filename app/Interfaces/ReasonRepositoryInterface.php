<?php

namespace App\Interfaces;

interface ReasonRepositoryInterface
{

    // public function getReasonBySchoolProgramId($schoolProgramId);
    public function getAllReasons();
    public function getReasonById($reasonId);
    public function getReasonByName($reasonName);
    public function deleteReason($reasonId);
    public function createReason(array $reasons);
    public function createReasons(array $reasonDetails);
    public function updateReason($reasonId, array $reasons);
    public function getAllReasonFromCRM();
}
