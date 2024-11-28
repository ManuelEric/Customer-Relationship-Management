<?php

namespace App\Actions\Positions;

use App\Interfaces\PositionRepositoryInterface;

class UpdatePositionAction
{
    private PositionRepositoryInterface $positionRepository;

    public function __construct(PositionRepositoryInterface $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }

    public function execute(
        $position_id,
        Array $new_position_details
    )
    {

        $new_position = $this->positionRepository->updatePosition($position_id, $new_position_details);


        return $new_position;
    }
}