<?php

namespace App\Actions\Positions;

use App\Interfaces\PositionRepositoryInterface;

class DeletePositionAction
{
    private PositionRepositoryInterface $positionRepository;

    public function __construct(PositionRepositoryInterface $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }

    public function execute(
        $position_id
    )
    {
        # delete position
        $deleted_position = $this->positionRepository->deletePosition($position_id);

        return $deleted_position;
    }
}