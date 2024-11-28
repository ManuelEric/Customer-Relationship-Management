<?php

namespace App\Services\Instance;

use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;

class SchoolService 
{
    protected ProgramRepositoryInterface $programRepository;
    protected ReasonRepositoryInterface $reasonRepository;

    public function __construct(ProgramRepositoryInterface $programRepository, ReasonRepositoryInterface $reasonRepository) 
    {
        $this->programRepository = $programRepository;
        $this->reasonRepository = $reasonRepository;
    }

    public function snSetAttributeSchoolDetail(Array $validated, $is_update = false)
    {
        $school_details = [];

        $represent_max_length = count($validated['schdetail_name']);
        for ($i = 0; $i < $represent_max_length; $i++) {
            if(!$is_update){
                $school_details[] = [
                    'sch_id' => $validated['sch_id'],
                    'schdetail_fullname' => $validated['schdetail_name'][$i],
                    'schdetail_email' => $validated['schdetail_mail'][$i],
                    'schdetail_grade' => $validated['schdetail_grade'][$i],
                    'schdetail_position' => $validated['schdetail_position'][$i],
                    'schdetail_phone' => $validated['schdetail_phone'][$i],
                    'is_pic' => $validated['is_pic'][$i],
                ];
            }else{
                $school_details = [
                    'sch_id' => $validated['sch_id'],
                    'schdetail_fullname' => $validated['schdetail_name'][$i],
                    'schdetail_email' => $validated['schdetail_mail'][$i],
                    'schdetail_grade' => $validated['schdetail_grade'][$i],
                    'schdetail_position' => $validated['schdetail_position'][$i],
                    'schdetail_phone' => $validated['schdetail_phone'][$i],
                    'is_pic' => $validated['is_pic'][$i],
                ];
            }
        }

        return $school_details;
    }
}