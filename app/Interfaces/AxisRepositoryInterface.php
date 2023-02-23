<?php

namespace App\Interfaces;

interface AxisRepositoryInterface
{
    public function getAxisByType($type);
    public function createAxis(array $axis);
    public function updateAxis($id, array $axis);
}
