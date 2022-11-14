<?php

namespace App\Repositories;

use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SubProgRepositoryInterface;
use App\Models\Program;
use DataTables;

class ProgramRepository implements ProgramRepositoryInterface 
{

    protected MainProgRepositoryInterface $mainProgRepository;
    protected SubProgRepositoryInterface $subProgRepository;

    public function __construct(MainProgRepositoryInterface $mainProgRepository, SubProgRepositoryInterface $subProgRepository)
    {
        $this->mainProgRepository = $mainProgRepository;
        $this->subProgRepository = $subProgRepository;
    }

    public function getAllProgramsDataTables()
    {
        return Datatables::eloquent(Program::query())->make(true);
    }

    public function getAllPrograms()
    {
        return Program::all();
    }

    public function getProgramById($programId)
    {
        return Program::whereProgId($programId);
    }

    public function deleteProgram($programId)
    {
        return Program::destroy($programId);
    }

    public function createProgram(array $programDetails)
    {
        $programDetails['prog_program'] = $programDetails['prog_name'];
        unset($programDetails['prog_name']);

        $programDetails['main_prog_id'] = $programDetails['prog_main'];
        unset($programDetails['prog_main']);

        $programDetails['sub_prog_id'] = $programDetails['prog_sub'];
        unset($programDetails['prog_sub']);
        
        $mainProg = $this->mainProgRepository->getMainProgById($programDetails['main_prog_id']);
        $subProg = $this->subProgRepository->getSubProgById($programDetails['sub_prog_id']);
        
        # fetch prog name & sub prog name
        $programDetails['prog_main'] = $mainProg->prog_name;
        $programDetails['prog_sub'] = $subProg->sub_prog_name;

        # disesuaikan dengan main_prog_id & sub_prog_id
        return Program::create($programDetails);
    }
    
    public function updateProgram($programId, array $newDetails)
    {
        # initialize
        $newDetails['prog_program'] = $newDetails['prog_name'];
        unset($newDetails['prog_name']);

        $newDetails['main_prog_id'] = $newDetails['prog_main'];
        unset($newDetails['prog_main']);

        $newDetails['sub_prog_id'] = $newDetails['prog_sub'];
        unset($newDetails['prog_sub']);
        
        $mainProg = $this->mainProgRepository->getMainProgById($newDetails['main_prog_id']);
        $subProg = $this->subProgRepository->getSubProgById($newDetails['sub_prog_id']);
        
        # fetch prog name & sub prog name
        $newDetails['prog_main'] = $mainProg->prog_name;
        $newDetails['prog_sub'] = $subProg->sub_prog_name;

        # disesuaikan dengan main_prog_id & sub_prog_id
        return Program::whereProgId($programId)->update($newDetails);
    }

    public function cleaningProgram()
    {
        $programs = Program::all();
        foreach ($programs as $program) 
        {
            # fetch the detail
            $detail = Program::where('prog_id', $program->prog_id)->first();

            # initialize
            if ($main_prog = $this->mainProgRepository->getMainProgByName($program->prog_main)) {

                $main_prog_id = $main_prog->id;
                $detail->main_prog_id = $main_prog_id;
            }

            if ($sub_prog = $this->subProgRepository->getSubProgBySubProgName($program->prog_sub)) {

                $sub_prog_id = $sub_prog->id;
                $detail->sub_prog_id = $sub_prog_id;
            }

            # update
            $detail->save();
            
        }
    }
}