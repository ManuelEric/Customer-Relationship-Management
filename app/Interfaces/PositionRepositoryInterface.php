<?php

namespace App\Interfaces;

interface PositionRepositoryInterface 
{
    public function getAllPositionDataTables();
    public function getAllPositions();
    public function getPositionByName($positionName);
    public function deletePosition($positionId);
    public function createPositions(array $positionDetails);
    public function createPosition(array $positionDetails);
    public function updatePosition($positionId, array $newDetails);
}