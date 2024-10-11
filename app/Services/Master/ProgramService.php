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
    # Get and merge program b2c, program b2b
    public function snGetAllPrograms(): Collection
    {
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        return $programsB2B->merge($programsB2BB2C);
    }
}