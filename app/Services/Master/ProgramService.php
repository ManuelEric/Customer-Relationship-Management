<?php

namespace App\Services\Master;

use App\Interfaces\ProgramRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProgramService
{
    protected ProgramRepositoryInterface $programRepository;

    public function __construct(ProgramRepositoryInterface $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    # Purpose:
    # Get and merge program b2b
    public function snGetProgramsB2b(): Collection
    {
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        return $programsB2B->merge($programsB2BB2C);
    }

    # Purpose:
    # Get and merge program b2c
    public function snGetProgramsB2c(): Collection
    {
        $b2cprograms = $this->programRepository->getAllProgramByType("B2C");
        $b2bb2cprograms = $this->programRepository->getAllProgramByType("B2B/B2C");
        return $b2cprograms->merge($b2bb2cprograms);
    }
}