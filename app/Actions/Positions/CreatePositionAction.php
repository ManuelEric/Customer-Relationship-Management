<?php

namespace App\Actions\Positions;

use App\Interfaces\PositionRepositoryInterface;

class CreatePositionAction
{
    private PositionRepositoryInterface $positionRepository;

    public function __construct(PositionRepositoryInterface $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }

    public function execute(
        Array $new_position_details
    )
    {
        # store new position
        $new_position = $this->positionRepository->createPosition($new_position_details);

        return $new_position;
    }
}